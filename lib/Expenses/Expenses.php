<?php

namespace Expenses;

use Config;

use PDO;
use Exception;

class Expenses {
  static $name = 'expenses';

  static $fields = array(
    'expenseid' =>  PDO::PARAM_INT,
    'date'      =>  PDO::PARAM_STR,
    'amount'    =>  PDO::PARAM_STR,
    'comment'   =>  PDO::PARAM_STR
  );

  protected $db;
  
  protected $loaded = false;
  protected $rowCount;
  
  protected $expenses = array();

  public function __construct(PDO $db) {
    $this->db = $db;
  }
  
  public function load($limit = 25, $page = 0) {
    if (! is_int($limit)) {
      throw new Exception('Specified $limit is not of type int');
    }
    
    if (! is_int($page)) {
      throw new Exception('Specified $page is not of type int');
    }
  
    $sql = "
      SELECT SQL_CALC_FOUND_ROWS " . $this->getFieldList() . "
      FROM " . Config::TABLE_PREFIX . "expenses as expenses
      ORDER BY expenses.date DESC
      LIMIT " . $page * $limit . ", " . $limit . "
    ";
    
    // prepare statement
    $query = $this->db->prepare($sql);
    
    // empty array for each row's values to be bound into
    $expense = array();
    
    // bind values to variables
    foreach ($this->getFields() as $fieldIndex => $field) {
      $query->bindColumn($field, $expense[$field], $this->getFieldType($fieldIndex));
    }
    
    // execute query
    $query->execute();
    
    // fetch results into bound attributes
    while ($query->fetch(PDO::FETCH_BOUND)) {
      
    }
    
    // count the rows selected
    $rowCountQuery = $this->db->prepare("SELECT FOUND_ROWS()");
    $rowCountQuery->execute();
    $rowCountQuery->bindColumn(1, $this->rowCount);
    $rowCountQuery->fetch(PDO::FETCH_BOUND);
    $rowCountQuery->closeCursor();
    
    // set loaded to true
    $this->loaded = true;
  }
  
  public function getFields() {
    $fields = array_keys(self::$fields);
    
    foreach ($fields as &$value) {
      $value = self::$name . "." . $value;
    }
    
    return $fields;
  }
  
  public function getFieldList() {
    $fields = $this->getFields();
    
    return implode(',', $fields);
  }
  
  public function getFieldType($index) {
    return self::$fields[$index];
  }
  
  public function getCount() {
    return $this->rowCount;
  }
}

?>