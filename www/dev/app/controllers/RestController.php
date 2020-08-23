<?php

namespace app\controllers;

use app\response\Response;
use app\config\HttpStatusCode;

class RestController
{

    /**
     * Standart response
     * @param $data
     * @param null $code
     * @return Response
     */
    public function response($data, $code = null): Response
    {
        return new Response($data, $code);
    }

    /**
     * Bad request response
     * @param $data
     * @return Response
     */
    public function responseBadRequest($data): Response
    {
        return new Response($data, HttpStatusCode::BAD_REQUEST);
    }
}