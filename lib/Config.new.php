<?php

class Config {
  const DATABASE_SERVER   = 'localhost';
  const DATABASE_NAME     = 'expenses';
  const DATABASE_USERNAME = '';
  const DATABASE_PASSWORD = '';
  const TABLE_PREFIX      = '';
  
  const DOCUMENT_ROOT     = '/var/www/';
  const SERVER_ROOT       = '/expense-tracker/';
  const TEMPLATE_DIR      = 'templates';
  
  const MINIMUM_USERNAME_LENGTH =   3;
  const MAXIMUM_USERNAME_LENGTH =   32;
}

?>