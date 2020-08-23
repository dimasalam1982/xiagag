<?php

namespace app\controllers;

use app\response\Response;

class SwaggerController
{
    public function actionGenerate()
    {
        $openapi = \OpenApi\scan(realpath('../app'));
        file_put_contents('../public/openapi/openapi_online.yaml',$openapi->toYaml());
        echo 'ok';
        exit(0);
    }
}