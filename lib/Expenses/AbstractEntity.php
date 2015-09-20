<?php

namespace Expenses;

use \DateTime;
use \InvalidArgumentException;

use Config;

/**
 * Description
 *
 * @author sean
 */
abstract class AbstractEntity
{    
    private $loaded = false;

    public function __construct()
    {
        
    }
        
    public function isLoaded() {
        return $this->loaded;
    }
    
    public function setLoaded($loaded) {
        $this->loaded = boolval($loaded);
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

        $existsQuery->bindParam(1, $id, ExpensesPDO::PARAM_INT);
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
        
        $query->bindParam(':database', $database, ExpensesPDO::PARAM_STR);
        $query->bindParam(':table', $table, ExpensesPDO::PARAM_STR);
        
        $query->execute();
        
        $query->bindColumn(1, $exists);
        $query->fetch(ExpensesPDO::FETCH_BOUND);
        
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
        
        $query->bindParam(':database', $database, ExpensesPDO::PARAM_STR);
        $query->bindParam(':table', $table, ExpensesPDO::PARAM_STR);
        $query->bindParam(':column', $column, ExpensesPDO::PARAM_STR);
        
        $query->execute();
        
        $query->bindColumn(1, $exists);
        $query->fetch(ExpensesPDO::FETCH_BOUND);
        
        return (bool) $exists;
    }
    
    /**
     * @param type $dateString
     * @return type
     */
    public static function validateDateString($dateString)
    {
        return (strtotime($dateString));
    }
    
    /**
     * Explicitly copies an array by value.
     *
     * @param array $source The array to copy
     * @return array The copied array
     */
    public static function array_copy(array $source)
    {
        $arr = array();

        foreach ($source as $key => $element) {
            if (is_array($element)) {
                $arr[$key] = array_copy($element);
            } else {
                $arr[$key] = $element;
            }
        }

        return $arr;
    }
    
    public static function getTableColumnIdentifier($table, $column) {
        return ":" . $table . "__" . $column;
    }
}

?>