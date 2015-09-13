<?php

require('include.php');

use Expenses\Expense;
use Expenses\ExpenseGroup;
use Expenses\TypeGroup;
use Expenses\LocationGroup;
use Expenses\Totals;

if (empty($do)) {
    $get = filter_input_array(
        INPUT_GET,
        array(
            'message'    =>  FILTER_SANITIZE_STRING
        )
    );
    
    /*
     * Get recent expenses
     */
    
    $expenses = new ExpenseGroup(
        array(),
        array(
            array(
                'column' => 'date',
                'direction' => ExpenseGroup::ORDER_DESC
            )
        )
    );
    
    $expenses->load();
    
    $totals = new Totals($expenses);

    echo $templates->render('expenses', ['expenses' => $expenses, 'message' => $get['message'], 'totals' => $totals->getTotals($user)]);
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
            'comment'       =>  array(
                                    'filter'    =>  FILTER_SANITIZE_STRING,
                                    'flags'     =>  FILTER_FLAG_NO_ENCODE_QUOTES
                                )
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
} elseif ($do === 'edit') {
    $get = filter_input_array(
        INPUT_GET,
        array(
            'id'    =>  FILTER_VALIDATE_INT
        )
    );
    
    /*
     * Load expense
     */
    
    try {
        $expense = new Expense($get['id']);
    } catch (InvalidArgumentException $e) {
        exit($templates->render('error', ['message' => 'Specified ID is invalid.']));
    }
    
    try {
        $expense->load();
    } catch (ObjectNotFoundException $e) {
        exit($templates->render('error', ['message' => 'Specified ID not found.']));
    }
    
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
            'comment'       =>  array(
                                    'filter'    =>  FILTER_SANITIZE_STRING,
                                    'flags'     =>  FILTER_FLAG_NO_ENCODE_QUOTES
                                )
        )
    );
    
    // get list of types
    $types = new TypeGroup(array(), array(array('column' => 'name', 'direction' => TypeGroup::ORDER_ASC)));
    $types->load();
    
    // get list of locations
    $locations = new LocationGroup(array(), array(array('column' => 'organisation', 'direction' => LocationGroup::ORDER_ASC)));
    $locations->load();
    
    if (! count($_POST)) {
        echo $templates->render('expenses-edit', ['expense' => $expense, 'types' => $types, 'locations' => $locations]);
    } else {
        // FIXME: validate
        $expense->setAttribute('date', $user->getUtcDateFromUserDate($post['date']));
        $expense->setAttribute('typeid', $post['typeid']);
        $expense->setAttribute('amount', $post['amount']);
        $expense->setAttribute('locationid', $post['locationid']);
        $expense->setAttribute('comment', $post['comment']);
        $expense->save();
        
        header('Location: index.php?message=editsuccess');
    }
} elseif ($do === 'delete') {
    $get = filter_input_array(
        INPUT_GET,
        array(
            'id'    =>  FILTER_VALIDATE_INT
        )
    );
    
    /*
     * Load expense
     */
    
    try {
        $expense = new Expense($get['id']);
    } catch (InvalidArgumentException $e) {
        exit($templates->render('error', ['message' => 'Specified ID is invalid.']));
    }
    
    try {
        $expense->load();
    } catch (ObjectNotFoundException $e) {
        exit($templates->render('error', ['message' => 'Specified ID not found.']));
    }
    
    /*
     * Process POST data
     */
    
    $post = filter_input_array(
        INPUT_POST,
        array(
            'confirm' =>  FILTER_VALIDATE_BOOLEAN
        )
    );
    
    if (! $post['confirm']) {        
        echo $templates->render('expenses-delete', ['expense' => $expense]);
    } else {
        $expense->delete();
        
        header('Location: index.php?message=deletesuccess');
    }
} elseif ($do === 'view') {
    $get = filter_input_array(
        INPUT_GET,
        array(
            'id'    =>  FILTER_VALIDATE_INT
        )
    );
    
    /*
     * Load expense
     */
    
    try {
        $expense = new Expense($get['id']);
    } catch (InvalidArgumentException $e) {
        exit($templates->render('error', ['message' => 'Specified ID is invalid.']));
    }
    
    try {
        $expense->load();
    } catch (ObjectNotFoundException $e) {
        exit($templates->render('error', ['message' => 'Specified ID not found.']));
    }

    echo $templates->render('expenses-view', ['expense' => $expense]);
}

?>