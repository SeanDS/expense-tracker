<?php

require('include.php');

//use Expenses\Expenses;
//
//$expenses = new Expenses($db);
//$expenses->load();

use Expenses\User;

//$user = User::create(array("username" => "sean", "password" => "test"));

$user = User::login("sean", "test");

use Expenses\ExpenseGroup;

$expenses = new ExpenseGroup();
$expenses->load();

// global user
$templates->addData(['user' => $user]);

// page title for template
$templates->addData(['title' => 'Index'], ['template']);

// expenses
$templates->addData(['expenses' => $expenses], ['expenses']);

echo $templates->render('index');

?>