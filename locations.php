<?php

require('include.php');

use Expenses\Location;
use Expenses\LocationGroup;
use Expenses\ExpenseGroup;
use Expenses\Totals;
use Expenses\ObjectNotFoundException;

use \InvalidArgumentException;

if (empty($do)) {
    $get = filter_input_array(
        INPUT_GET,
        array(
            'message'    =>  FILTER_SANITIZE_STRING
        )
    );
    
    $locations = new LocationGroup();
    $locations->load();

    echo $templates->render('locations', ['locations' => $locations, 'message' => $get['message']]);
} elseif ($do === 'new') {
    /*
     * Process POST data
     */
    
    $post = filter_input_array(
        INPUT_POST,
        array(
            'organisation'  =>  array(
                                    'filter'    =>  FILTER_SANITIZE_STRING,
                                    'flags'     =>  FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_STRIP_HIGH
                                ),
            'address'       =>  array(
                                    'filter'    =>  FILTER_SANITIZE_STRING,
                                    'flags'     =>  FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_STRIP_HIGH
                                )
        )
    );
    
    if (! count($post)) {
        echo $templates->render('locations-new');
    } else {
        Location::create($post);
        
        header('Location: locations.php?message=newsuccess');
    }
} elseif ($do === 'edit') {
    $get = filter_input_array(
        INPUT_GET,
        array(
            'id'    =>  FILTER_VALIDATE_INT
        )
    );
    
    /*
     * Load location
     */
    
    try {
        $location = new Location($get['id']);
    } catch (InvalidArgumentException $e) {
        exit($templates->render('error', ['message' => 'Specified ID is invalid.']));
    }
    
    try {
        $location->load();
    } catch (ObjectNotFoundException $e) {
        exit($templates->render('error', ['message' => 'Specified ID not found.']));
    }
    
    /*
     * Process POST data
     */
    
    $post = filter_input_array(
        INPUT_POST,
        array(
            'organisation'  =>  array(
                                    'filter'    =>  FILTER_SANITIZE_STRING,
                                    'flags'     =>  FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_STRIP_HIGH
                                ),
            'address'       =>  array(
                                    'filter'    =>  FILTER_SANITIZE_STRING,
                                    'flags'     =>  FILTER_FLAG_NO_ENCODE_QUOTES
                                )
        )
    );
    
    if (! count($_POST)) {
        echo $templates->render('locations-edit', ['location' => $location]);
    } else {        
        // FIXME: validate
        $location->setAttribute('organisation', $post['organisation']);
        $location->setAttribute('address', $post['address']);
        $location->save();
        
        header('Location: locations.php?message=editsuccess');
    }
} elseif ($do === 'moveexpenses') {
    $get = filter_input_array(
        INPUT_GET,
        array(
            'id'    =>  FILTER_VALIDATE_INT
        )
    );
    
    /*
     * Load location
     */
    
    try {
        $location = new Location($get['id']);
    } catch (InvalidArgumentException $e) {
        exit($templates->render('error', ['message' => 'Specified ID is invalid.']));
    }
    
    try {
        $location->load();
    } catch (ObjectNotFoundException $e) {
        exit($templates->render('error', ['message' => 'Specified ID not found.']));
    }
    
    /*
     * Get expenses for this location
     */
    
    $expenses = new ExpenseGroup(
        array(
            array(
                'column'    =>  'locationid',
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
            'newlocationid' =>  FILTER_VALIDATE_INT
        )
    );
    
    if (is_null($post['newlocationid'])) {
        // get list of locations
        $locations = new LocationGroup(array(), array(array('column' => 'organisation', 'direction' => LocationGroup::ORDER_ASC)));
        $locations->load();
        
        if ($locations->count() <= 1) {
            // only this type exists
            exit($templates->render('error', ['message' => 'No other locations exist to move expenses to.']));
        }
        
        echo $templates->render('locations-move-expenses', ['location' => $location, 'locations' => $locations, 'expenses' => $expenses]);
    } else {
        $expenses->moveToLocation($post['newlocationid']);
        
        header('Location: locations.php?message=moveexpensessuccess');
    }
} elseif ($do === 'delete') {
    $get = filter_input_array(
        INPUT_GET,
        array(
            'id'    =>  FILTER_VALIDATE_INT
        )
    );
    
    /*
     * Load location
     */
    
    try {
        $location = new Location($get['id']);
    } catch (InvalidArgumentException $e) {
        exit($templates->render('error', ['message' => 'Specified ID is invalid.']));
    }
    
    try {
        $location->load();
    } catch (ObjectNotFoundException $e) {
        exit($templates->render('error', ['message' => 'Specified ID not found.']));
    }
    
    /*
     * Check for default location ID
     */
    
    if ($location->getId() == Location::$defaultLocationId) {
        exit($templates->render('error', ['message' => 'Default location cannot be deleted.']));
    }
    
    /*
     * Process POST data
     */
    
    $post = filter_input_array(
        INPUT_POST,
        array(
            'newlocationid' =>  FILTER_VALIDATE_INT
        )
    );
    
    if (is_null($post['newlocationid'])) {
        // get list of locations
        $locations = new LocationGroup(array(), array(array('column' => 'organisation', 'direction' => LocationGroup::ORDER_ASC)));
        $locations->load();
        
        echo $templates->render('locations-delete', ['location' => $location, 'locations' => $locations]);
    } else {
        $location->delete($post['newlocationid']);
        
        header('Location: locations.php?message=deletesuccess');
    }
} elseif ($do === 'view') {
    $get = filter_input_array(
        INPUT_GET,
        array(
            'id'    =>  FILTER_VALIDATE_INT
        )
    );
    
    /*
     * Load location
     */
    
    try {
        $location = new Location($get['id']);
    } catch (InvalidArgumentException $e) {
        exit($templates->render('error', ['message' => 'Specified ID is invalid.']));
    }
    
    try {
        $location->load();
    } catch (ObjectNotFoundException $e) {
        exit($templates->render('error', ['message' => 'Specified ID not found.']));
    }
    
    /*
     * Load expenses
     */
    
    $expenses = new ExpenseGroup(
        array(
            array(
                'column'    =>  'locationid',
                'operator'  => ExpenseGroup::OPERATOR_EQUALS,
                'value'     =>  $location->getId()
            )
        ),
        array(
            array(
                'column'    =>  'date',
                'direction' => ExpenseGroup::ORDER_DESC
            )
        )
    );
    
    $expenses->load();
    $totals = new Totals($expenses);

    echo $templates->render('locations-view', ['location' => $location, 'expenses' => $expenses, 'totals' => $totals->getTotals($user)]);
}

?>