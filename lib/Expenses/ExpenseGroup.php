<?php

namespace Expenses;

use Expenses\NotLoadedException;
use Expenses\Type;
use Expenses\Location;

use Config;

/**
 * Description
 *
 * @author sean
 */
class ExpenseGroup extends AbstractGroup
{
    protected static $objectClass = Expense::class;
    
    public function getTotalExpenses() {
        $query = $this->getQueryObject();
        
        $query->execute();
        
        // get singlular object
        $obj = static::$objectClass;
        
        // table
        $table = Config::TABLE_PREFIX . $obj::$table;
        
        $query->bindColumn('amount', $amount, $obj::$attributeTypes['amount']);
        
        $total = 0;
        
        // fetch results into bound attributes
        while ($query->fetch(ExpensesPDO::FETCH_BOUND)) {
            $total += floatval($amount);
        }
        
        return $total;
    }
    
    public function moveToType($newTypeId) {
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
    
    public function moveToLocation($newLocationId) {
        global $db;
        
        if (! $this->isLoaded()) {
            throw new NotLoadedException();
        }
        
        $newLocation = new Location($newLocationId);
        $newLocation->load();
        
        $db->beginTransaction();
        
        foreach ($this->get() as $expense) {
            $expense->setAttribute('locationid', $newLocation->getId());
            $expense->save();
        }
        
        $db->commit();
    }
}

?>