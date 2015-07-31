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
abstract class AbstractSingular
{
    private $id;
    private $table;
    private $idColumn;
    
    protected $attributes = array();
    
    private $loaded = false;

    public function __construct($table, $idColumn, $id)
    {
        if (! self::validateTableName($table)) {
            throw new InvalidArgumentException("The specified table is invalid.");
        }
        
        if (! self::validateColumnName($idColumn, $table)) {
            throw new InvalidArgumentException("The specified id column is invalid.");
        }
        
        if (! self::validateId($id)) {
            throw new InvalidArgumentException("The specified id is invalid.");
        }
        
        $this->table = $table;
        $this->idColumn = $idColumn;
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
        
        $table = Config::TABLE_PREFIX . $this->table;

        // list of columns to select
        $columns = array_keys(static::$attributeTypes);

        // full expression
        $sql = "
            SELECT " . implode(", ", $columns) . "
            FROM " . $table . "
            WHERE " . $this->idColumn . " = :id
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

            return true;
        } else {
            throw new ObjectNotFoundException($this->table, $this->idColumn, $this->id);
        }
    }
    
    public function isLoaded()
    {
        return $this->loaded;
    }
    
    public function setLoaded($loaded) {
        $this->loaded = boolval($loaded);
    }
    
    public function getId() {
        if (! $this->isLoaded()) {
            throw new NotLoadedException();
        }
        
        return $this->id;
    }
    
    public static function idExists($id, $table)
    {
        global $db;
        
        if (! self::validateId($id)) {
            throw new InvalidArgumentException("Specified id is invalid.");
        }
        
        if (! self::validateTableName($table)) {
            throw new InvalidArgumentException("Specified table is invalid.");
        }

        $existsQuery = $db->prepare("
            SELECT EXISTS(SELECT 1 FROM " . $table . " WHERE userid = ?)
        ");

        $existsQuery->bindParam(1, $id, PDO::PARAM_INT);
        $existsQuery->execute();

        $existsQuery->bindColumn(1, $exists);
        $existsQuery->fetch();

        return (bool) $exists;
    }
    
    public static function validateTableName($table) {
        global $db;
        
        $table = Config::TABLE_PREFIX . $table;
        $database = Config::DATABASE_NAME;
        
        $query = $db->prepare("
            SELECT EXISTS(
                SELECT 1
                FROM INFORMATION_SCHEMA.TABLES
                WHERE
                    TABLE_SCHEMA = :database
                    AND TABLE_NAME = :table
            )
        ");
        
        $query->bindParam(':database', $database, PDO::PARAM_STR);
        $query->bindParam(':table', $table, PDO::PARAM_STR);
        
        $query->execute();
        
        $query->bindColumn(1, $exists);
        $query->fetch(PDO::FETCH_BOUND);
        
        return (bool) $exists;
    }
    
    public static function validateColumnName($column, $table) {
        global $db;
        
        $table = Config::TABLE_PREFIX . $table;
        $database = Config::DATABASE_NAME;
        
        $query = $db->prepare("
            SELECT EXISTS(
                SELECT 1
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE
                    TABLE_SCHEMA = :database
                    AND TABLE_NAME = :table
                    AND COLUMN_NAME = :column
            )
        ");
        
        $query->bindParam(':database', $database, PDO::PARAM_STR);
        $query->bindParam(':table', $table, PDO::PARAM_STR);
        $query->bindParam(':column', $column, PDO::PARAM_STR);
        
        $query->execute();
        
        $query->bindColumn(1, $exists);
        $query->fetch(PDO::FETCH_BOUND);
        
        return (bool) $exists;
    }
    
    /**
     * 
     * 
     * From http://stackoverflow.com/questions/19271381/correctly-determine-if-date-string-is-a-valid-date-in-that-format
     * 
     * @param type $dateString
     * @return type
     */
    public static function validateDateString($dateString)
    {
        $date = DateTime::createFromFormat(DB_DATE_FORMAT, $dateString);
        
        return $date && $date->format(DB_DATE_FORMAT) == $dateString;
    }
    
    /**
     * Checks whether the specified attribute exists.
     * 
     * @param type $attribute
     * @return type
     */
    public function attributeExists($attribute)
    {        
        return array_key_exists($attribute, $this->attributes);
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
    
    public static function validateId($id)
    {
        $valid = filter_var(
            $id,
            FILTER_VALIDATE_INT,
            array(
                'options'   =>  array(
                    'min_range' =>  1
                )
            )
        );

        return ($valid === false) ? false : true;
    }
}

?>