<?php

namespace Expenses;

use Expenses\NotLoadedException;
use Expenses\Type;

/**
 * Description
 *
 * @author sean
 */
class ExpenseGroup extends AbstractGroup
{
    protected static $objectClass = Expense::class;
    
    function moveToType($newTypeId) {
        global $db;
        
        if (! $this->isLoaded()) {
            throw new NotLoadedException();
        }
        
        $newType = new Type($newTypeId);
        $newType->load();
        
        $db->beginTransaction();
        
        foreach ($this->get() as $expense) {
            $expense->setAttribute('typeid', $newType->getId());
            $expense->save();
        }
        
        $db->commit();
    }
}

?>