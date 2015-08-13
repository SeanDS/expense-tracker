<?php

namespace Expenses;

use \Exception;

/**
 * Description of NotLoadedException
 *
 * @author sean
 */
class NotLoadedException extends Exception
{
    public function __construct()
    {
        parent::__construct("Object has not been loaded.");
    }
}

?>
