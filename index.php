<?php

require('config.php');

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
 * Register class autoloader
 */

require('lib/Autoload.php');

// expenses autoloader
$autoloader = new Autoload('Expenses', Config::DOCUMENT_ROOT . 'lib');
$autoloader->register();

/*
 * Create database connection function
 */

use \PDO;
use \PDOException;

try {
    // create the database connection
    $db = new PDO(
        "mysql:host=" . Config::DATABASE_SERVER . ";dbname=" . Config::DATABASE_NAME,
        Config::DATABASE_USERNAME,
        Config::DATABASE_PASSWORD,
        array(
            PDO::ATTR_PERSISTENT    =>  true,
            PDO::ATTR_ERRMODE       =>  PDO::ERRMODE_EXCEPTION
        )
    );
} catch (PDOException $exception) {
    echo "Error connecting to database";
}

use Expenses\Expenses;

$expenses = new Expenses($db);
$expenses->load();

echo "Expenses: " . $expenses->getCount();

?>