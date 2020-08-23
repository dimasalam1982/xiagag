<?php

namespace app\request;

class Request
{
    private static $post;
    private static $get;

    public static function parse()
    {
        $data = file_get_contents('php://input');;
        self::$post = json_decode($data, true);

        $urlInfo = parse_url($_SERVER['REQUEST_URI']);

        if (!empty($urlInfo['query'])){
            $args = explode('&', $urlInfo['query']);
            foreach ($args as $arg) {
                [$name, $value] = explode('=', $arg);
                self::$get[$name] = $value;
            }
        }
    }

    public static function post($field = null)
    {
        if ($field) {
            return isset(self::$post[$field]) ? self::$post[$field] : null;
        }

        return self::$post;
    }

    public static function get($field = null)
    {
        if ($field) {
            return isset(self::$get[$field]) ? self::$get[$field] : null;
        }

        return self::$get;
    }
}