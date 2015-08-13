<?php

require('include.php');

use Expenses\Type;
use Expenses\TypeGroup;
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

    // global user
    $templates->addData(['user' => $user]);

    // page title for template
    $templates->addData(['title' => 'Types'], ['template']);

    // types
    $templates->addData(['types' => $types, 'message' => $get['message']], ['types-list']);

    echo $templates->render('types');
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
        echo $templates->render('error', ['message' => 'Specified ID is invalid.']);
        exit();
    }
    
    try {
        $type->load();
    } catch (ObjectNotFoundException $e) {
        echo $templates->render('error', ['message' => 'Specified ID not found.']);
        exit();
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
        $type->setAttribute('name', $post['name']);
        $type->setAttribute('description', $post['description']);
        $type->save();
        
        header('Location: types.php?message=success');
    }
}

?>