<?php

namespace app;

use app\controllers\MainController;
use app\response\Response;

class Router
{
    private static $routes = array();

    private function __construct() {}
    private function __clone() {}

    public static function route(string $pattern, callable $callback)
    {
        $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
        self::$routes[$pattern] = $callback;
    }

    public static function execute(string $url): Response
    {
        try{

            foreach (self::$routes as $pattern => $callback)
            {
                if (preg_match($pattern, $url, $params))
                {
                    array_shift($params);
                    return call_user_func_array($callback, array_values($params));
                }
            }

            return (new MainController)->actionNotFound();

        } catch (\Throwable $e) {

            return (new MainController)->actionUnknowError($e->getMessage().'; '.$e->getFile().'; '.$e->getLine());

        }

    }
}