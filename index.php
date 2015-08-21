<?php

require('include.php');

use Expenses\Expense;
use Expenses\ExpenseGroup;
use Expenses\TypeGroup;
use Expenses\LocationGroup;

if (empty($do)) {
    $expenses = new ExpenseGroup();
    $expenses->load();

    // expenses
    $templates->addData(['expenses' => $expenses], ['expenses']);

    echo $templates->render('expenses');
} elseif ($do === 'new') {
    /*
     * Process POST data
     */
    
    $post = filter_input_array(
        INPUT_POST,
        array(
            'date'          =>  FILTER_SANITIZE_STRING,
            'typeid'        =>  FILTER_VALIDATE_INT,
            'amount'        =>  FILTER_VALIDATE_FLOAT,
            'locationid'    =>  FILTER_VALIDATE_INT,
            'comment'       =>  FILTER_SANITIZE_STRING
        )
    );
    
    // get list of types
    $types = new TypeGroup(array(), array(array('column' => 'name', 'direction' => TypeGroup::ORDER_ASC)));
    $types->load();
    
    // get list of locations
    $locations = new LocationGroup(array(), array(array('column' => 'organisation', 'direction' => LocationGroup::ORDER_ASC)));
    $locations->load();
    
    if (! count($post)) {
        echo $templates->render('expenses-new', ['types' => $types, 'locations' => $locations]);
    } else {
        Expense::create($post);
        
        header('Location: index.php?message=newsuccess');
    }
}

?>