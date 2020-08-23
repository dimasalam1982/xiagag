<?php

namespace app\database;

use PDO;

class Database
{

    private static $db;

    private static $instance;

    private function __construct()
    {
    }

    public static function getInstance($emptyDb = false, $force = false)
    {
        if (is_null(self::$db) || $force) {
            self::pdoConnect($emptyDb);
        }

        if (is_null(self::$instance) || $force) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private static function pdoConnect($emptyDb)
    {
        try {
            self::$db = new PDO(
                'mysql:dbname=' . ($emptyDb ? '' : getenv('MYSQL_DATABASE')) . ';host=' . getenv('MYSQL_HOST'),
                getenv('MYSQL_USER'),
                getenv('MYSQL_PASSWORD'),
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
                )
            );
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function getAll(string $sql, $args = [])
    {
        $query = self::$db->prepare($sql);
        $query->execute($args);
        return $query->fetchAll();
    }

    public function getRow(string $sql, $args = [])
    {
        $query = self::$db->prepare($sql);
        $query->execute($args);
        return $query->fetch();
    }

    public function sql(string $sql)
    {
        $query = self::$db->prepare($sql);
        $query->execute();
    }

    public function insert(string $sql, array $args)
    {
        $query = self::$db->prepare($sql);
        $query->execute($args);

        $errors = $this->isError($query);

        return empty($errors) ? self::$db->lastInsertId() : $errors;
    }

    public function isError($result):array
    {
        return [];
    }
}