<?php

namespace app;

use app\Router;
use app\request\Request;
use app\models\User;
use app\controllers\MainController;
use app\service\Registry;

class Application
{
    public static function run()
    {
        Request::parse();

        $user = (new User)::authOrCreateNew();

        Registry::add('user', $user);

        self::setRoutes();

        (Router::execute($_SERVER['REQUEST_URI']))->response();
    }

    private static function setRoutes()
    {
        Router::route('/', function () {
            return (new MainController)->actionIndex();
        });

        Router::route('/(\w+)/(\w+)/*(.*)', function ($controller, $action, $params = null) {

            if ($params) {
                $params = explode('/', $params);
                $params = is_array($params) ? array_values($params) : [$params];
            } else {
                $params = [];
            }

            $controller = 'app\controllers\\' . ucfirst($controller) . 'Controller';

            $action = 'action' . ucfirst($action);

            if (class_exists($controller) && method_exists($controller, $action)) {
                return call_user_func_array([(new $controller), $action], array_values($params));
            } else {
                return (new MainController)->actionNotFound();
            }
        });
    }
}