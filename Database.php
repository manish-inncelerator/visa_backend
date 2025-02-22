<?php
// Require Composer's autoloader.
require 'vendor/autoload.php';

// Using Medoo namespace.
use Medoo\Medoo;

// Connect the database.
$database = new Medoo([
    'type' => 'mysql',
    'host' => 'localhost',
    'port' => '3304',
    'database' => 'portify',
    'username' => 'root',
    'password' => ''
]);

// Set the timezone to UTC.

date_default_timezone_set('UTC');
