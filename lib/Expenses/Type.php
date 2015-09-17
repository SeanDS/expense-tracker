<?php

namespace Expenses;

use \InvalidArgumentException;

class Type extends AbstractSingular
{
    public static $attributeTypes = array(
        'typeid'        =>  ExpensesPDO::PARAM_INT,
        'parenttypeid'  =>  ExpensesPDO::PARAM_INT,
        'name'          =>  ExpensesPDO::PARAM_STR,
        'description'   =>  ExpensesPDO::PARAM_STR
    );
    
    public static $table = 'types';
    public static $idColumn = 'typeid';
    public static $defaultTypeId = 1;
    
    /**
     * Sets expenses with this type to specified other type before deleting this
     * type.
     * 
     * @override AbstractSingular::delete
     * @global type $db
     */
    public function delete($newTypeId) {
        if ($this->getId() === self::$defaultTypeId) {
            throw new InvalidArgumentException("Default type cannot be deleted");
        }
        
        global $db;
        
        $newType = new Type($newTypeId);
        $newType->load();
        
        $expenses = new ExpenseGroup(
            array(
                array(
                    'column'    =>  'typeid',
                    'operator'  =>  ExpenseGroup::OPERATOR_EQUALS,
                    'value'     =>  $this->getId()
                )
            )
        );
        
        // start database transaction
        $db->beginTransaction();
        
        // load and move expenses associated with this type to new one
        $expenses->load();
        $expenses->moveToType($newType->getId());
        
        // delete type
        parent::delete();
        
        // commit changes to database
        $db->commit();
    }
    
    public function hasParent() {
        return ($this->getAttribute('parenttypeid') > 0);
    }
    
    public function getParent() {
        if (! ($this->hasParent())) {
            throw new Exception("This type has no parent.");
        }
        
        $parent = new Type($this->getAttribute('parenttypeid'));
        $parent->load();
        
        return $parent;
    }
    
    public function getFullName() {
        $name = $this->getAttribute('name');
        
        if ($this->hasParent()) {
            // add parent's name before this name            
            $name = $this->getParent()->getFullName() . " -> " . $name;
        }
        
        return $name;
    }
    
    public function getFormattedDescription() {
        return nl2br($this->getAttribute('description'));
    }
    
    public function getExpenseCount() {
        if (! $this->isLoaded()) {
            throw new NotLoadedException();
        }
        
        $expenses = new ExpenseGroup(
            array(
                array(
                    'column'    =>  'typeid',
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
