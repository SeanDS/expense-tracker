<?php

require('include.php');

use Expenses\Expenses;

$expenses = new Expenses($db);
$expenses->load();

echo "Expenses: " . $expenses->getCount();

use Expenses\User;

//$user = User::create(array("username" => "sean", "password" => "test"));

print_r(User::login("sean", "test"));

?>