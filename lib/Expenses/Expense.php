<?php

namespace Expenses;

use \Exception;
use \InvalidArgumentException;

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
        global $user;
        
        if (array_key_exists('date', $data)) {
            if (! self::validateDateString($data['date'])) {
                throw new Exception("Invalid date specified.");
            }
            
            // format date as UTC
            $data['date'] = $user->getUtcDateFromUserDate($data['date']);
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
    
    public function getDescription() {
        return sprintf("£%.2f at %s at %s", $this->getAttribute('amount'), $this->getLocation()->getDescription(), $this->getDate());
    }
    
    public function getFormattedComment() {
        return nl2br($this->getAttribute('comment'));
    }
    
    public function getDate() {
        global $user;
        
        return $user->getUserDateFromUtcDate($this->getAttribute('date'));
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
            (mb_strlen($comment) <= 255) // database-defined maximum length
        );
    }
}

?>