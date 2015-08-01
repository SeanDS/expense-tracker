<?php

namespace Expenses;

use PDO;
use DateTime;

use Config;
use InvalidArgumentException;
use NotLoadedException;

/**
 * Description
 *a
 * @author sean
 */
abstract class AbstractSingular extends AbstractEntity
{
    private $id;
    
    protected $attributes = array();

    public function __construct($id)
    {
        parent::__construct();
        
        if (! self::validateId($id)) {
            throw new InvalidArgumentException("The specified id is invalid.");
        }
        
        $this->id = $id;
    }
    
    public static abstract function create($data);
    
    public function save($attribute = null) {
        // $attribute specifies the attribute to save
        // null means save all
    }
    
    public function load() {
        global $db;

        /*
         * create and execute SQL query
         */

        // list of columns to select
        $columns = array_keys(static::$attributeTypes);

        // full expression
        $sql = "
            SELECT " . implode(", ", $columns) . "
            FROM " . Config::TABLE_PREFIX . static::$table . "
            WHERE " . static::$idColumn . " = :id
        ";

        // prepare statement
        $query = $db->prepare($sql);

        // bind parameters
        $query->bindParam(':id', $this->id, PDO::PARAM_INT);
        
        // execute query
        $query->execute();

        /*
         * build attribute array
         */

        // column iterator
        $columnCount = 1;

        // bind columns to attributes
        foreach ($columns as $column) {
            $query->bindColumn($columnCount, $this->attributes[$column], static::$attributeTypes[$column]);

            $columnCount++;
        }

        // fetch results into bound attributes
        if ($query->fetch(PDO::FETCH_BOUND)) {
            // set loaded
            $this->setLoaded(true);
        } else {
            throw new ObjectNotFoundException(static::$table, static::$idColumn, $this->id);
        }
    }
    
    public static function fromAttributes($id, $attributes) {
        $cls = static::class;
        
        $obj = new $cls($id);
        $obj->setAttributes($attributes);
        
        return $obj;
    }
    
    public function getId() {
        if (! $this->isLoaded()) {
            throw new NotLoadedException();
        }
        
        return $this->id;
    }
    
    /**
     * Checks whether the specified attribute exists.
     * 
     * @param type $attribute
     * @return type
     */
    public function attributeExists($attribute)
    {        
        return array_key_exists($attribute, static::$attributeTypes);
    }
    
    /**
     * Get a specified attribute.
     * 
     * @param type $attribute   The attribute to load
     * @return type
     * @throws InvalidArgumentException
     */
    public function getAttribute($attribute)
    {
        if ($this->attributeExists($attribute)) {
            // save new value
            return $this->attributes[$attribute];
        } else {
            throw new InvalidArgumentException(
                sprintf('The specified attribute, %s, does not exist or has not been loaded.', $attribute)
            );
        }
    }

    /**
     * Sets the given attribute to the given value.
     *
     * @param type $attribute   The attribute to set
     * @param type $value       The new value to give the attribute
     * @param type $save        Whether to save immediately
     */
    public function setAttribute($attribute, $value, $save = false)
    {
        // FIXME: validate attributes (setAttributes() below doesn't do so)
        
        if ($this->attributeExists($attribute)) {
            // save new value
            $this->attributes[$attribute] = $value;
        } else {
            throw new InvalidArgumentException(
                sprintf('The specified attribute, %s, does not exist or has not been loaded.', $attribute)
            );
        }
        
        if ($save) {
            $this->save($attribute);
        }
    }
    
    public function setAttributes($attributes) {
        if (! is_array($attributes)) {
            throw new InvalidArgumentException("Specified attributes argument is not an array.");
        }
        
        // clear the attributes
        $this->attributes = array();
        
        // extract only proper attributes
        foreach (array_keys(static::$attributeTypes) as $attribute) {
            if (! key_exists($attribute, $attributes)) {
                throw new InvalidArgumentException(sprintf("Specified attributes do not contain the key %s.", $attribute));
            }
            
            $this->setAttribute($attribute, $attributes[$attribute]);
        }
    }
}

?>