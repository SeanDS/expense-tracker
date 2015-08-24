<?php

require('include.php');

use Expenses\Type;
use Expenses\TypeGroup;
use Expenses\ExpenseGroup;
use Expenses\ObjectNotFoundException;
use \InvalidArgumentException;

if (empty($do)) {
    $get = filter_input_array(
        INPUT_GET,
        array(
            'message'    =>  FILTER_SANITIZE_STRING
        )
    );
    
    $types = new TypeGroup();
    $types->load();

    // page title for template
    $templates->addData(['title' => 'Types'], ['template']);

    echo $templates->render('types', ['types' => $types, 'message' => $get['message']]);
} elseif ($do === 'new') {
    /*
     * Process POST data
     */
    
    $post = filter_input_array(
        INPUT_POST,
        array(
            'name'          =>  FILTER_SANITIZE_STRING,
            'description'   =>  FILTER_SANITIZE_STRING
        )
    );
    
    if (! count($post)) {
        echo $templates->render('types-new');
    } else {
        Type::create($post);
        
        header('Location: types.php?message=newsuccess');
    }
} elseif ($do === 'edit') {
    $get = filter_input_array(
        INPUT_GET,
        array(
            'id'    =>  FILTER_VALIDATE_INT
        )
    );
    
    /*
     * Load type
     */
    
    try {
        $type = new Type($get['id']);
    } catch (InvalidArgumentException $e) {
        exit($templates->render('error', ['message' => 'Specified ID is invalid.']));
    }
    
    try {
        $type->load();
    } catch (ObjectNotFoundException $e) {
        exit($templates->render('error', ['message' => 'Specified ID not found.']));
    }
    
    /*
     * Process POST data
     */
    
    $post = filter_input_array(
        INPUT_POST,
        array(
            'name'          =>  FILTER_SANITIZE_STRING,
            'description'   =>  FILTER_SANITIZE_STRING
        )
    );
    
    if (! count($_POST)) {
        echo $templates->render('types-edit', ['type' => $type]);
    } else {
        // FIXME: validate
        $type->setAttribute('name', $post['name']);
        $type->setAttribute('description', $post['description']);
        $type->save();
        
        header('Location: types.php?message=editsuccess');
    }
} elseif ($do === 'moveexpenses') {
    $get = filter_input_array(
        INPUT_GET,
        array(
            'id'    =>  FILTER_VALIDATE_INT
        )
    );
    
    /*
     * Load type
     */
    
    try {
        $type = new Type($get['id']);
    } catch (InvalidArgumentException $e) {
        exit($templates->render('error', ['message' => 'Specified ID is invalid.']));
    }
    
    try {
        $type->load();
    } catch (ObjectNotFoundException $e) {
        exit($templates->render('error', ['message' => 'Specified ID not found.']));
    }
    
    /*
     * Get expenses for this type
     */
    
    $expenses = new ExpenseGroup(
        array(
            array(
                'column'    =>  'typeid',
                'operator'  =>  ExpenseGroup::OPERATOR_EQUALS,
                'value'     =>  $get['id']
            )
        )
    );
    
    $expenses->load();
    
    /*
     * Process POST data
     */
    
    $post = filter_input_array(
        INPUT_POST,
        array(
            'newtypeid' =>  FILTER_VALIDATE_INT
        )
    );
    
    if (is_null($post['newtypeid'])) {
        // get list of types
        $types = new TypeGroup(array(), array(array('column' => 'name', 'direction' => TypeGroup::ORDER_ASC)));
        $types->load();
        
        if ($types->count() <= 1) {
            // only this type exists
            exit($templates->render('error', ['message' => 'No other types exist to move expenses to.']));
        }
        
        echo $templates->render('types-move-expenses', ['type' => $type, 'types' => $types, 'expenses' => $expenses]);
    } else {
        $expenses->moveToType($post['newtypeid']);
        
        header('Location: types.php?message=moveexpensessuccess');
    }
} elseif ($do === 'delete') {
    $get = filter_input_array(
        INPUT_GET,
        array(
            'id'    =>  FILTER_VALIDATE_INT
        )
    );
    
    /*
     * Load type
     */
    
    try {
        $type = new Type($get['id']);
    } catch (InvalidArgumentException $e) {
        echo $templates->render('error', ['message' => 'Specified ID is invalid.']);
        exit();
    }
    
    try {
        $type->load();
    } catch (ObjectNotFoundException $e) {
        exit($templates->render('error', ['message' => 'Specified ID not found.']));
    }
    
    /*
     * Check for default type ID
     */
    
    if ($type->getId() == Type::$defaultTypeId) {
        exit($templates->render('error', ['message' => 'Default type cannot be deleted.']));
    }
    
    /*
     * Process POST data
     */
    
    $post = filter_input_array(
        INPUT_POST,
        array(
            'newtypeid' =>  FILTER_VALIDATE_INT
        )
    );
    
    if (is_null($post['newtypeid'])) {
        // get list of types
        $types = new TypeGroup(array(), array(array('column' => 'name', 'direction' => TypeGroup::ORDER_ASC)));
        $types->load();
        
        echo $templates->render('types-delete', ['type' => $type, 'types' => $types]);
    } else {
        $type->delete($post['newtypeid']);
        
        header('Location: types.php?message=deletesuccess');
    }
} elseif ($do === 'view') {
    $get = filter_input_array(
        INPUT_GET,
        array(
            'id'    =>  FILTER_VALIDATE_INT
        )
    );
    
    /*
     * Load type
     */
    
    try {
        $type = new Type($get['id']);
    } catch (InvalidArgumentException $e) {
        exit($templates->render('error', ['message' => 'Specified ID is invalid.']));
    }
    
    try {
        $type->load();
    } catch (ObjectNotFoundException $e) {
        exit($templates->render('error', ['message' => 'Specified ID not found.']));
    }

    echo $templates->render('types-view', ['type' => $type]);
}

?>