<?php

require('include.php');

use Expenses\Expense;
use Expenses\ExpenseGroup;
use Expenses\TypeGroup;
use Expenses\LocationGroup;

use \DateTime;
use \DateInterval;

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
    
    $expenses = new ExpenseGroup();
    $expenses->load();
    
    /*
     * Get expenditure
     */
    
    $totals = array();
    $userTime = new DateTime($user->getCurrentUserDate(), $user->getTimeZone());
    
    // today
    $userMidnight = $userTime->setTime(0, 0, 0);
    $todayGroup = new ExpenseGroup(
        array(
            array(
                'column'    =>  'date',
                'operator'  => ExpenseGroup::OPERATOR_GREATER_THAN_EQUALS,
                'value'     =>  $userMidnight->format(DB_DATE_FORMAT)
            )
        )
    );
    $totals[] = array(
        'range'     =>  'Today',
        'amount'    =>  $todayGroup->getTotalExpenses()
    );
    
    // last 24 hours
    $userOneDayAgo = $userTime->sub(new DateInterval('P1D'));
    $lastDayGroup = new ExpenseGroup(
        array(
            array(
                'column'    =>  'date',
                'operator'  => ExpenseGroup::OPERATOR_GREATER_THAN_EQUALS,
                'value'     =>  $userOneDayAgo->format(DB_DATE_FORMAT)
            )
        )
    );
    $totals[] = array(
        'range'     =>  'Last 24 hours',
        'amount'    =>  $lastDayGroup->getTotalExpenses()
    );
    
    // all time
    $allTimeGroup = new ExpenseGroup();
    $totals[] = array(
        'range'     =>  'All Time',
        'amount'    =>  $allTimeGroup->getTotalExpenses()
    );

    echo $templates->render('expenses', ['expenses' => $expenses, 'message' => $get['message'], 'totals' => $totals]);
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
            'comment'       =>  FILTER_SANITIZE_STRING
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