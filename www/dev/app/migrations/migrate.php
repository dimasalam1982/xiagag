<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', E_ALL);

require_once '../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../..');
$dotenv->load();

$db = app\database\Database::getInstance(true);

$data = $db->sql('DROP DATABASE IF EXISTS '.getenv('MYSQL_DATABASE'));

$db->sql('CREATE DATABASE '.getenv('MYSQL_DATABASE').' DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci');

$db = app\database\Database::getInstance(false, true);

$db->sql('
    CREATE TABLE question (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        uid VARCHAR(50) UNIQUE,
        title VARCHAR(255)
    );
');

$db->sql('CREATE INDEX `idx_question_uid` ON question (uid)');

$db->sql('
    CREATE TABLE variant (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        question_id INTEGER,
        title VARCHAR(255)
    );
');

$db->sql('ALTER TABLE variant ADD CONSTRAINT fk_variant_question_id FOREIGN KEY (question_id) REFERENCES question(id);');

$db->sql('
    CREATE TABLE user (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        uid VARCHAR(50) UNIQUE,
        name VARCHAR(50)
    );
');

$db->sql('CREATE INDEX `idx_user_uid` ON user (uid)');

$db->sql('
    CREATE TABLE answer (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        user_id INT,
        question_id INT,
        variant_id INT,
        FOREIGN KEY (user_id)  REFERENCES user (id),
        FOREIGN KEY (question_id)  REFERENCES question (id),
        FOREIGN KEY (variant_id)  REFERENCES variant (id)
    );
');