<?php

namespace app\models;

use app\database\Database;

abstract class BaseModel
{
    protected $db;

    protected $errors;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Add one validation error
     * @param string $key
     * @param string $text
     * @return array
     */
    public function addError(string $key, string $text): array
    {
        $this->errors[$key] = $text;
        return $this->errors;
    }

    /**
     * Validation errors
     * @return array|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function validate()
    {
        $this->errors = null;
    }

    /**
     * If saving will be correct that return self otherwise return null
     * @return mixed
     */
    abstract public function save();
}