<?php

namespace Expenses;

use \Exception;

/**
 * Description
 *
 * @author sean
 */
class NoRowsAffectedException extends Exception
{
    public function __construct()
    {
        parent::__construct("No rows affected.");
    }
}

?>
