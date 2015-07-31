<?php

require('include.php');

//use Expenses\Expenses;
//
//$expenses = new Expenses($db);
//$expenses->load();

use Expenses\User;

//$user = User::create(array("username" => "sean", "password" => "test"));

$user = User::login("sean", "test");

use Expenses\Expense;

class Expenses {
    function getCount() {
        return 0;
    }
}

$expenses = new Expenses();

$templates->addData(['username' => $user->getAttribute('username')]);
$templates->addData(['title' => 'Index'], ['template']);
$templates->addData(['expenses' => $expenses], ['expenses']);

echo $templates->render('index');

?>