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
    public function getTree() {
        $objects = $this->get();
        
        $refs = array();
        $tree = array();
        
        // sort objects by parentid into tree
        foreach ($objects as $object)
        {
            $id = $object->getId();
            $parentId = $object->getAttribute('parenttypeid');
            
            $ref =& $refs[$object->getId()];

            $ref['type'] = $object;

            if ($parentId == 0)
            {
                $tree[$id] =& $ref;
            }
            else
            {
                $refs[$parentId]['children'][$id] =& $ref;
            }
        }
        
        return $tree;
    }
}

?>