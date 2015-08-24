<?php

namespace Expenses;

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
    
    /**
     * Sets expenses with this location to specified other location before deleting this
     * location.
     * 
     * @override AbstractSingular::delete
     * @global type $db
     */
    public function delete($newLocationId) {
        if ($this->getId() === self::$defaultLocationId) {
            throw new InvalidArgumentException("Default location cannot be deleted");
        }
        
        global $db;
        
        $newLocation = new Location($newLocationId);
        $newLocation->load();
        
        $expenses = new ExpenseGroup(
            array(
                array(
                    'column'    =>  'locationid',
                    'operator'  =>  ExpenseGroup::OPERATOR_EQUALS,
                    'value'     =>  $this->getId()
                )
            )
        );
        
        // start database transaction
        $db->beginTransaction();
        
        // load and move expenses associated with this location to new one
        $expenses->load();
        $expenses->moveToLocation($newLocation->getId());
        
        // delete location
        parent::delete();
        
        // commit changes to database
        $db->commit();
    }
    
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
