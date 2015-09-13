<?php

// constants
define('DB_DATE_FORMAT', 'Y-m-d H:i:s');

// set charset
ini_set('default_charset', "utf-8");

// configuration settings
require('lib/Config.php');

// libraries
require('vendor/autoload.php');

// define some primary error handling functions
function errorHandler($errno, $errstr, $errfile, $errline, $context = null) {
  $ignore = array(
    E_NOTICE,
    E_STRICT,
    E_USER_NOTICE
  );

  if (in_array($errno, $ignore)) {
    // error is not one we care about
    return true;
  }
  
  echo "<h1>Error</h1>";
  echo "<pre>";
  echo sprintf("Error number: %s\nError string: %s\nError file: %s\nError line: %s", $errno, $errstr, $errfile, $errline);
  echo "</pre>";

  /* Don't execute PHP internal error handler */
  return true;
}

function fatalErrorHandler() {
  // working directory might have changed during web server's shutdown function,
  // so change it back
  chdir(Config::DOCUMENT_ROOT);

  $error = error_get_last();

  if ($error !== null) {
    $errno   = $error["type"];
    $errfile = $error["file"];
    $errline = $error["line"];
    $errstr  = $error["message"];

    errorHandler($errno, $errstr, $errfile, $errline);
  }
}

function exceptionHandler(Exception $exception) {
  echo "<h1>Exception</h1>";
  echo "<pre>";
  echo sprintf("Exception: %s", $exception);
  echo "</pre>";

  exit();
}

// register error/exception handlers to produce visual messages for users
set_error_handler('errorHandler');
set_exception_handler('exceptionHandler');
register_shutdown_function('fatalErrorHandler');

/*
 * Create the template engine
 */

$templates = new League\Plates\Engine('templates');

/*
 * Create database connection function
 */

use Expenses\ExpensesPDO;
use \PDOException;

try {
    // create the database connection
    $db = new ExpensesPDO(
        "mysql:host=" . Config::DATABASE_SERVER . ";dbname=" . Config::DATABASE_NAME,
        Config::DATABASE_USERNAME,
        Config::DATABASE_PASSWORD,
        array(
            PDO::ATTR_PERSISTENT            =>  true,
            PDO::ATTR_ERRMODE               =>  PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND    => "SET NAMES utf8"
        )
    );
    
    // set strict queries
    $db->exec("SET SESSION sql_mode='STRICT_ALL_TABLES'");
} catch (PDOException $exception) {
    echo "Error connecting to database";
}

/*
 * Filter the $_GET['do'] parameter
 */

$do = filter_input(INPUT_GET, 'do', FILTER_SANITIZE_STRING);

/*
 * Start session
 */

use Expenses\User;

session_name('expense-tracker');
session_set_cookie_params(7 * 24 * 60 * 60, Config::SERVER_ROOT);
session_start();

// detect user session
if (array_key_exists('userId', $_SESSION)) {
    $userId = $_SESSION['userId'];

    $user = new User($userId);
    $user->load();

    $templates->addData(['user' => $user]);
} else {
    if (basename($_SERVER['SCRIPT_NAME']) !== 'login.php') {
        header('Location: login.php');
    }
}

?>