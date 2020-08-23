<?php

namespace app\service;

class Registry
{
    private static $registry = [];

    public static function add($name, $item)
    {
        self::$registry[$name] = $item;
    }

    public static function get($name)
    {
        return self::$registry[$name] ?? null;
    }
}