<?php

namespace Expenses;

use \InvalidArgumentException;

use Config;

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
    
    public static function create($data) {
        global $db;
        
        /*
         * check $data has all the necessary stuff
         */
        
        foreach (array_keys(static::$attributeTypes) as $attribute) {
            if (! ($attribute === static::$idColumn)) {
                if (! array_key_exists($attribute, $data)) {
                    throw new InvalidArgumentException(sprintf("Specified data array must contain key %s.", $attribute));
                }
            }
        }

        $identifiers = array();
        
        foreach (array_keys(static::$attributeTypes) as $attribute) {
            if (! ($attribute === static::$idColumn)) {
                $identifiers[$attribute] = self::getTableColumnIdentifier(static::$table, $attribute);
            }
        }

        $sql = "INSERT INTO " . static::$table . " (" . implode(', ', array_keys($identifiers)) . ") VALUES (" . implode(', ', $identifiers) . ")";
        
        $insertQuery = $db->prepare($sql);

        // bind values
        foreach ($data as $attribute => $value) {
            // use bindValue instead of bindParam to avoid issues with pass-by-reference
            if (! ($attribute === static::$idColumn)) {
                $insertQuery->bindValue($identifiers[$attribute], $value, static::$attributeTypes[$attribute]);
            }
        }

        // execute query
        $insertQuery->execute();
        
        // check affected rows
        if ($insertQuery->rowCount() === 0) {
            throw new NoRowsAffectedException();
        }
        
        return $db->lastInsertId();
    }
    
    /**
     * 
     * @global type $db
     * @param type $attribute
     * @param type $checkRowCount   Check whether any rows were affected and throw an error if not
     * @throws NotLoadedException
     * @throws Exception
     * @throws NoRowsAffectedException
     */
    public function save($attribute = null, $checkRowCount = false) {        
        global $db;

        if (! $this->isLoaded()) {
            throw new NotLoadedException();
        }
        
        // $attribute specifies the attribute to save
        // null means save all
        if (is_null($attribute)) {
            $attributes = array_keys($this->attributes);
        } else {
            if (! in_array($attribute, array_keys(static::$attributeTypes))) {
                throw new Exception('Specified attribute is not valid.');
            }
            
            $attributes = array($attribute);
        }

        // start a transaction
        $db->beginTransaction();

        /*
         * Update table
         */

        $columns = array();
        $identifiers = array();

        foreach ($attributes as $attribute) {
            $identifiers[$attribute] = self::getTableColumnIdentifier(static::$table, $attribute);
            
            $columns[] = $attribute . " = " . $identifiers[$attribute];
        }

        $sql = "UPDATE " . static::$table . " SET " . implode(', ', $columns) . " WHERE " . static::$idColumn . " = " . self::getTableColumnIdentifier(static::$table, static::$idColumn);

        $tableQuery = $db->prepare($sql);

        foreach ($attributes as $attribute) {
            $tableQuery->bindValue($identifiers[$attribute], $this->getAttribute($attribute), static::$attributeTypes[$attribute]);
        }

        // set WHERE clause
        $tableQuery->bindParam(self::getTableColumnIdentifier(static::$table, static::$idColumn), $this->getId(), static::$attributeTypes[static::$idColumn]);

        $tableQuery->execute();

        if($checkRowCount && $tableQuery->rowCount() === 0) {
            // no rows were affected, so the object doesn't exist
            // TODO: this can also be caused by an invalid value resulting in
            // no affected rows (e.g. trying to set a float field to 1,25)

            // roll back
            $db->rollBack();

            // throw exception
            throw new NoRowsAffectedException();
        }

        $tableQuery->closeCursor();

        // everything seems to have worked, so commit the transaction
        $db->commit();
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
            WHERE " . static::$idColumn . " = " . self::getTableColumnIdentifier(static::$table, static::$idColumn) . "
        ";

        // prepare statement
        $query = $db->prepare($sql);

        // bind ID column
        $query->bindParam(self::getTableColumnIdentifier(static::$table, static::$idColumn), $this->getId(), static::$attributeTypes[static::$idColumn]);
        
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
        if ($query->fetch(ExpensesPDO::FETCH_BOUND)) {
            // set loaded
            $this->setLoaded(true);
        } else {
            throw new ObjectNotFoundException(static::$table, static::$idColumn, $this->getId());
        }
    }
    
    public function delete() {
        global $db;
        
        if (! $this->isLoaded()) {
            throw new NotLoadedException();
        }
        
        $db->beginTransaction();
        
        $deleteQuery = $db->prepare("
            DELETE FROM " . Config::TABLE_PREFIX . static::$table . "
            WHERE " . static::$idColumn . " = " . self::getTableColumnIdentifier(static::$table, static::$idColumn) . "
        ");
        
        $deleteQuery->bindParam(self::getTableColumnIdentifier(static::$table, static::$idColumn), $this->getId(), static::$attributeTypes[static::$idColumn]);
        $deleteQuery->execute();
        
        if (! $deleteQuery->rowCount()) {
            // roll back
            $db->rollBack();

            // throw exception
            throw new NoRowsAffectedException();
        }

        $deleteQuery->closeCursor();
        
        $db->commit();
    }
    
    public static function fromAttributes($id, $attributes) {
        $cls = static::class;
        
        $obj = new $cls($id);
        $obj->setAttributes($attributes);
        $obj->setLoaded(true);
        
        return $obj;
    }
    
    public function getId() {
        if (! $this->id) {
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