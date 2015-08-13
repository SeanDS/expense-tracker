<?php

require('include.php');

use Expenses\ExpenseGroup;

if (empty($do)) {
    $expenses = new ExpenseGroup();
    $expenses->load();

    // global user
    $templates->addData(['user' => $user]);

    // page title for template
    $templates->addData(['title' => 'Index'], ['template']);

    // expenses
    $templates->addData(['expenses' => $expenses], ['expenses-list']);

    echo $templates->render('index');
}

?>