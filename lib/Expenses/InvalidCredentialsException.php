<?php

namespace Expenses;

use Exception;

/**
 * Description
 *
 * @author sean
 */
class InvalidCredentialsException extends Exception
{
    public function __construct()
    {
        parent::__construct("Specified credentials are invalid.");
    }
}

?>
