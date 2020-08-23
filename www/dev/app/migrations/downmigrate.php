<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', E_ALL);

require_once '../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../..');
$dotenv->load();


$db = app\database\Database::getInstance(true);

$data = $db->sql('DROP DATABASE IF EXISTS '.getenv('MYSQL_DATABASE'));