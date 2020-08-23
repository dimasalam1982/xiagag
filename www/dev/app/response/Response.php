<?php

namespace app\response;

use app\response\ResponseInterface;
use app\config\HttpStatusCode;

class Response implements ResponseInterface
{

    const FORMAT_JSON = 'json';

    private $data;

    private $code;

    public function getData()
    {
        return $this->data;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function formatResponse($format = null)
    {
        $format = $format ? $format : self::FORMAT_JSON;

        $data = [
            'data' => $this->data,
            'status' => $this->code === HttpStatusCode::OK
        ];

        switch ($format) {
            case self::FORMAT_JSON:
                $response = json_encode($data, JSON_UNESCAPED_UNICODE);
                $contentType = 'application/json';
                break;
            default:
                $response = $data;
                $contentType = 'text';
        }

        header('Content-Type: ' . $contentType);

        return $response;
    }

    public function __construct($data, $code = null)
    {
        $this->data = $data;
        $this->code = $code ? $code : HttpStatusCode::OK;
    }

    public function response()
    {
        http_response_code($this->code);

        echo $this->formatResponse();

        exit(0);
    }
}