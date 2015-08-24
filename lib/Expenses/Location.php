<?php

namespace Expenses;

use \Exception;
use \InvalidArgumentException;

use Config;

class Location extends AbstractSingular
{
    public static $attributeTypes = array(
        'locationid'    =>  ExpensesPDO::PARAM_INT,
        'organisation'  =>  ExpensesPDO::PARAM_STR,
        'address'       =>  ExpensesPDO::PARAM_STR
    );
    
    public static $table = 'locations';
    public static $idColumn = 'locationid';
    public static $defaultLocationId = 1;
    
    public function getDescription() {
        return $this->getAttribute('organisation') . ", " . $this->getBriefAddress();
    }
    
    public function getBriefAddress() {
        return str_replace(["\r\n", "\r", "\n"], ", ", $this->getAttribute('address'));
    }
    
    public function getFormattedAddress() {
        return nl2br($this->getAttribute('address'));
    }
    
    public function getExpenseCount() {
        if (! $this->isLoaded()) {
            throw new NotLoadedException();
        }
        
        $expenses = new ExpenseGroup(
            array(
                array(
                    'column'    =>  'locationid',
                    'operator'  =>  ExpenseGroup::OPERATOR_EQUALS,
                    'value'     =>  $this->getId()
                )
            )
        );
        
        $expenses->load();
        
        return $expenses->count();
    }
}

?>
