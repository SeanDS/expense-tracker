<?php

namespace Expenses;

/**
 * Description
 *
 * @author sean
 */
class TypeGroup extends AbstractGroup
{
    protected static $objectClass = Type::class;
    
    /**
     * List of types sorted into a tree hierarchy
     */
    public function toTree($rootId = 0) {
        return new IntTree($this->get(), "typeid", "parenttypeid");
    }
}

?>