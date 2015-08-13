<?php

namespace Expenses;

use \Exception;

/**
 * Description
 *
 * @author sean
 */
class ObjectNotFoundException extends Exception
{
    public function __construct($table, $idColumn, $id)
    {
        parent::__construct(
            sprintf('Object %s = %s does not exist in table %s.', $idColumn, $id, $table)
        );
    }
}

?>
