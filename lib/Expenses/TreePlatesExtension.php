<?php

namespace Expenses;

use Config;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class TreePlatesExtension implements ExtensionInterface {
    public function register(Engine $engine) {
        $engine->registerFunction('tree', [$this, 'getObject']);
    }

    public function getObject() {
        return $this;
    }

    public function toList($tree, $selectedTypeId, $ignoreTypeIds = null, $templates = null) {
        if (is_null($templates)) {
            // make NEW engine using existing templates directory
            $templates = new Engine(Config::TEMPLATE_DIR);
        }
        
        if (is_null($ignoreTypeIds)) {
            $ignoreTypeIds = array();
        }
        
        $html = "";
        
        foreach ($tree as $branch) {
            if (! in_array($branch['type']->getId(), $ignoreTypeIds)) {
                $html .= $templates->render('types-select-option', ['type' => $branch['type'], 'selectedTypeId' => $selectedTypeId]);
            }
            
            if (array_key_exists('children', $branch)) {
                $html .= $this->toList($branch['children'], $selectedTypeId, $ignoreTypeIds, $templates);
            }
        }
        
        return $html;
    }
}
