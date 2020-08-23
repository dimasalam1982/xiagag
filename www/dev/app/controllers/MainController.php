<?php

namespace app\controllers;

use app\config\HttpStatusCode;

class MainController extends RestController
{

    public function actionIndex()
    {
        return $this->response('Works!');
    }

    public function actionNotFound()
    {
        return $this->response('Not found', HttpStatusCode::NOT_FOUND);
    }

    public function actionUnknowError($message)
    {
        if (getenv('DEBUG') != 'true') {
            $message = 'Fatal error';
        }
        return $this->responseBadRequest($message);
    }
}