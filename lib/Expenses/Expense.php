<?php

namespace Expenses;

use \Exception;
use \InvalidArgumentException;

use Config;

class Expense extends AbstractSingular {
    public static $attributeTypes = array(
        'expenseid'     =>  ExpensesPDO::PARAM_INT,
        'date'          =>  ExpensesPDO::PARAM_STR,
        'typeid'        =>  ExpensesPDO::PARAM_INT,
        'locationid'    =>  ExpensesPDO::PARAM_INT,
        'amount'        =>  ExpensesPDO::PARAM_STR,
        'comment'       =>  ExpensesPDO::PARAM_STR
    );
    
    public static $table = 'expenses';
    public static $idColumn = 'expenseid';
    
    public static function create($data) {
        global $db;
        
        if (array_key_exists('date', $data)) {
            if (! self::validateDateString($data['date'])) {
                throw new Exception("Invalid date specified.");
            }
        } else {
            throw new Exception("Invalid date specified.");
        }
        
        if (array_key_exists('amount', $data)) {
            if (! self::validateAmount($data['amount'])) {
                throw new Exception("Invalid amount specified.");
            }
        } else {
            throw new InvalidArgumentException("Specified data array must include amount key.");
        }
        
        if (array_key_exists('comment', $data)) {
            if (! self::validateComment($data['comment'])) {
                throw new Exception("Invalid comment specified.");
            }
        } else {
            $data['comment'] = "";
        }
        
        parent::create($data);
    }
    
    public function getDate($user, $descriptive = true) {
        // TODO: check user is valid object
        return $user->formatDate($this->getAttribute('date'), $descriptive);
    }
    
    public function getType() {
        $type = new Type($this->getAttribute('typeid'));
        $type->load();
        
        return $type;
    }
    
    public function getLocation() {
        $location = new Location($this->getAttribute('locationid'));
        $location->load();
        
        return $location;
    }
    
    public static function validateAmount($amount) {        
        return (
            (floatval($amount) >= 0)
        );
    }
    
    public static function validateComment($comment) {
        return (
            (is_string($comment)) &&
            (strlen($comment) <= 255) // database-defined maximum length
        );
    }
}

?>