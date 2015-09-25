<?php

namespace Expenses;

/**
 * Description
 *
 * @author sean
 */
class IntTree {
    protected $objects;
    protected $idField;
    protected $parentIdField;
    
    private $tree;
    
    public function __construct($objects, $idField, $parentIdField) {
        $this->objects = $objects;
        $this->idField = $idField;
        $this->parentIdField = $parentIdField;
    }
    
    private function getNestedArray() {
        if (is_null($this->tree)) {
            $refs = array();
            $this->tree = array();

            // sort objects by parentid into tree
            foreach ($this->objects as $object)
            {
                $id = $object->getAttribute($this->idField);
                $parentId = $object->getAttribute($this->parentIdField);

                $ref =& $refs[$id];

                $ref['type'] = $object;

                if ($parentId == 0)
                {
                    $this->tree[$id] =& $ref;
                }
                else
                {
                    $refs[$parentId]['children'][$id] =& $ref;
                }
            }
        }
        
        return $this->tree;
    }
    
    public function getChildrenCount($nodeId) {
        return count($this->getBranch($nodeId));
    }
    
    public function getBranches() {
        return $this->getBranch(0);
    }
    
    public function getBranch($nodeId) {
        if (! self::validatePositiveInt($nodeId)) {
            throw new Exception("Invalid root id specified.");
        }
        
        $tree = $this->getNestedArray();
        
        if ($nodeId > 0) {
            return $tree[$nodeId];
        } else {
            return $tree;
        }
    }
    
    public static function validatePositiveInt($value)
    {
        $valid = filter_var(
            $value,
            FILTER_VALIDATE_INT,
            array(
                'options'   =>  array(
                    'min_range' =>  0
                )
            )
        );

        return ($valid === false) ? false : true;
    }
}

?>