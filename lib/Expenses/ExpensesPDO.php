<?php

namespace Expenses;

use \PDO;

/**
 * Based on http://www.kennynet.co.uk/2008/12/02/php-pdo-nested-transactions/.
 */
class ExpensesPDO extends PDO {   
    // current number of open transactions
    protected $openTransactions = 0;
    
    public function beginTransaction() {
        if ($this->openTransactions == 0) {
            parent::beginTransaction();
        } else {
            $this->exec("SAVEPOINT LEVEL{$this->openTransactions}");
        }
        
        $this->openTransactions++;
    }

    public function commit() {
        $this->openTransactions--;
        
        if ($this->openTransactions == 0) {
            parent::commit();
        } else {
            $this->exec("RELEASE SAVEPOINT LEVEL{$this->openTransactions}");
        }
    }

    public function rollBack() {
        $this->openTransactions--;

        if ($this->openTransactions == 0) {
            parent::rollBack();
        } else {
            $this->exec("ROLLBACK TO SAVEPOINT LEVEL{$this->openTransactions}");
        }
    }
}

?>