<?php
namespace Shisa\HTTPClient\Exceptions;

use Shisa\HTTPClient\HTTP\Response;

class ResponseError extends HTTPClientException
{
    public $response;

    public function __construct($code, $message, Response $response) {
        parent::__construct($message, $code);

        $this->response = $response;
    }
}