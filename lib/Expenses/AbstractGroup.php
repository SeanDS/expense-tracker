<?php

namespace Expenses;

use \InvalidArgumentException;

use Config;

/**
 * Description
 *
 * @author sean
 */
abstract class AbstractGroup extends AbstractEntity
{
    const OPERATOR_EQUALS                   =   1;
    const OPERATOR_GREATER_THAN             =   2;
    const OPERATOR_LESS_THAN                =   3;
    const OPERATOR_GREATER_THAN_EQUALS      =   4;
    const OPERATOR_LESS_THAN_EQUALS         =   5;
    const OPERATOR_NOT_EQUAL_TO             =   6;

    const ORDER_ASC                         =   1;
    const ORDER_DESC                        =   2;
    
    private $objects = array();
    
    // WHERE clause array (
    //      0   =>  array(
    //                  column,
    //                  operator,
    //                  value
    //              ),
    //      1...
    //  )
    private $whereCriteria;
    
    // ORDER BY array, in order of precedence (
    // table => array(
    //      0   =>  array(
    //                  column,
    //                  direction
    //              ),
    //      1...
    //  )
    private $orderBy;
    
    /*
     * row limits
     */
    
    private $startRow;
    private $rowLimit;
    
    /*
     * row count
     */
    
    private $countRows;
    private $rowCount;
    
    public function __construct($whereCriteria = array(), $orderBy = array(), $startRow = 0, $rowLimit = null, $countRows = false)
    {
        parent::__construct();
        
        self::validateWhereCriteria($whereCriteria);
        self::validateOrderBy($orderBy);
        self::validateStartRow($startRow);
        
        if ($rowLimit !== null) {
            self::validateRowLimit($rowLimit);
        }
        
        $this->whereCriteria = $whereCriteria;        
        $this->orderBy = $orderBy;
        $this->startRow = $startRow;
        $this->rowLimit = $rowLimit;
        $this->countRows = ($countRows) ? true : false;
    }
    
    protected function getQueryObject() {
        global $db;

        /*
         * get columns
         */
        
        // get singlular object
        $obj = static::$objectClass;
        
        // table
        $table = Config::TABLE_PREFIX . $obj::$table;

        /*
         * process WHERE clauses
         */
        
        $whereClauses = array();
        
        foreach ($this->whereCriteria as $criterion) {
            $column = $criterion['column'];

            switch ($criterion['operator']) {
                case self::OPERATOR_GREATER_THAN:
                    $operator = ">";
                    break;
                case self::OPERATOR_GREATER_THAN_EQUALS:
                    $operator = ">=";
                    break;
                case self::OPERATOR_LESS_THAN:
                    $operator = "<";
                    break;
                case self::OPERATOR_LESS_THAN_EQUALS:
                    $operator = "<=";
                    break;
                case self::OPERATOR_NOT_EQUAL_TO:
                    $operator = "<>";
                    break;
                case self::OPERATOR_EQUALS:
                default:
                    $operator = "=";
                    break;
            }

            $whereClauses [] = $table . "." . $column . " " . $operator . " " . self::getTableColumnIdentifier($table, $column);

            static $whereClausesExist = true;
        }
        
        if ($whereClausesExist) {
            $whereClause = "WHERE " . implode(" AND ", $whereClauses);
        } else {
            $whereClause = "";
        }
        
        /*
         * process ORDER BY clauses
         */
        
        $orderByClauses = array();
        
        foreach ($this->orderBy as $criterion) {
            $column = $criterion['column'];

            switch ($criterion['direction']) {
                case self::ORDER_ASC:
                    $order = "ASC";
                    break;
                case self::ORDER_DESC:
                default:
                    $order = "DESC";
                    break;
            }

            $orderByClauses [] = $table . "." . $column . " " . $order;

            static $orderByClausesExist = true;
        }
        
        if ($orderByClausesExist) {
            $orderByClause = "ORDER BY " . implode(", ", $orderByClauses);
        } else {
            $orderByClause = "";
        }
        
        /*
         * process limits
         */
        
        $limitString = "";
        
        if ($this->rowLimit !== null) {
            $limitString = "LIMIT " . $this->startRow . ", " . $this->rowLimit;
        }

        /*
         * create and execute SQL query
         */

        // full expression
        $sql = "SELECT " . (($this->countRows) ? "SQL_CALC_FOUND_ROWS " : "") . implode(", ", array_keys($obj::$attributeTypes)) . " FROM " . $table . " " . $whereClause . " " . $orderByClause . " " . $limitString;
        
        // prepare statement
        $query = $db->prepare($sql);

        /*
         * bind parameters
         */
        
        // where parameters
        foreach ($this->whereCriteria as $criterion) {
            $query->bindParam(self::getTableColumnIdentifier($table, $criterion['column']), $criterion['value'], $criterion['type']);
        }
        
        return $query;
    }
    
    protected function bindQuery($query) {
        global $db;
        
        // get singlular object
        $obj = static::$objectClass;
        
        /*
         * build attribute array
         */

        $attributes = array();

        // column iterator
        $columnCount = 1;

        foreach (array_keys($obj::$attributeTypes) as $column) {
            $query->bindColumn($columnCount, $attributes[$column]);

            $columnCount++;
        }
        
        // fetch results and put into attributes
        while ($query->fetch(ExpensesPDO::FETCH_BOUND)) {            
            $id = $attributes[$obj::$idColumn];

            // this is a nasty hack to copy the attributes by value, else every
            // object's attributes will point to those of the last object
            $theseAttributes = self::array_copy($attributes);
            
            // store new object
            $this->objects[] = $obj::fromAttributes($id, $theseAttributes);
        }

        // set loaded to true
        $this->setLoaded(true);

        if ($this->countRows) {
            // count the rows selected
            $rowCountQuery = $db->prepare("SELECT FOUND_ROWS()");
            $rowCountQuery->execute();
            $rowCountQuery->bindColumn(1, $this->rowCount);
            $rowCountQuery->fetch(ExpensesPDO::FETCH_BOUND);
            $rowCountQuery->closeCursor();
        }
    }
    
    public function load() {
        // get query
        $query = $this->getQueryObject();

        // execute query
        $query->execute();
        
        // bind results
        $this->bindQuery($query);

        // set loaded to true
        $this->setLoaded(true);
    }
    
    public function get() {
        if (! $this->isLoaded()) {
            throw new NotLoadedException();
        }
        
        return $this->objects;
    }
    
    public function count() {
        return count($this->get());
    }
    
    public static function validateWhereCriteria($whereCriteria) {
        if (! is_array($whereCriteria)) {
            throw new InvalidArgumentException('Specified where criteria parameter is not an array.');
        }
        
        $obj = static::$objectClass;
        
        foreach ($whereCriteria as $criterion) {  
            if (! array_key_exists('column', $criterion) || ! self::validateColumnName($criterion['column'], $obj::$table)) {
                throw new InvalidArgumentException("A specified column is invalid.");
            }

            if (! array_key_exists('operator', $criterion) || ! self::validateOperator($criterion['operator'])) {
                throw new InvalidArgumentException("A specified operator is invalid.");
            }
            
            // TODO: check values against their specified types
            if (! array_key_exists('value', $criterion)) {
                throw new InvalidArgumentException("A specified value is not present.");
            }
        }
    }
    
    public static function validateOrderBy($orderBy) {
        if (! is_array($orderBy)) {
            throw new InvalidArgumentException('Specified order by parameter is not an array.');
        }
        
        $obj = static::$objectClass;
        
        foreach ($orderBy as $order) {
            if (! array_key_exists('column', $order) || ! self::validateColumnName($order['column'], $obj::$table)) {
                throw new InvalidArgumentException("A specified column is invalid.");
            }

            if (! array_key_exists('direction', $order) || ! self::validateDirection($order['direction'])) {
                throw new InvalidArgumentException("A specified direction is invalid.");
            }
        }
    }
    
    public static function validateOperator($operator) {
        $valid = filter_var(
            $operator,
            FILTER_VALIDATE_INT,
            array(
                'options'   =>  array(
                    'min_range' =>  1
                )
            )
        );

        return ($valid === false) ? false : true;
    }
    
    public static function validateDirection($direction) {
        $valid = filter_var(
            $direction,
            FILTER_VALIDATE_INT,
            array(
                'options'   =>  array(
                    'min_range' =>  1
                )
            )
        );

        return ($valid === false) ? false : true;
    }
    
    public static function validateStartRow($startRow) {
        $valid = filter_var(
            $startRow,
            FILTER_VALIDATE_INT,
            array(
                'options'   =>  array(
                    'min_range' =>  0
                )
            )
        );

        if ($valid === false) {
            throw new InvalidArgumentException("Specified start row is invalid.");
        }
    }
    
    public static function validateRowLimit($rowLimit) {
        $valid = filter_var(
            $rowLimit,
            FILTER_VALIDATE_INT,
            array(
                'options'   =>  array(
                    'min_range' =>  1
                )
            )
        );

        if ($valid === false) {
            throw new InvalidArgumentException("Specified row limit is invalid.");
        }
    }
    
    public function getWhereCriteria() {
        if (! $this->isLoaded()) {
            throw new NotLoadedException();
        }
        
        return $this->whereCriteria;
    }
    
    public function getOrderBy() {
        if (! $this->isLoaded()) {
            throw new NotLoadedException();
        }
        
        return $this->orderBy;
    }
    
    public function getStartRow() {
        if (! $this->isLoaded()) {
            throw new NotLoadedException();
        }
        
        return $this->startRow;
    }
    
    public function getRowLimit() {
        if (! $this->isLoaded()) {
            throw new NotLoadedException();
        }
        
        return $this->rowLimit;
    }
}

?>