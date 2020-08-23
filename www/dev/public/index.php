<?php

require_once '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();

if (getenv('DEBUG') == 'true') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    ini_set('error_reporting', E_ALL);
}

define('BASE_URL', getenv('BASE_URL'));

use app\Application;

Application::run();

