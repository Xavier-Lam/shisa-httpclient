<?php
namespace Shisa\HTTPClient\Exceptions;

use Shisa\HTTPClient\HTTP\Response;

class HTTPError extends HTTPClientException
{
    public $response;

    public function __construct(Response $response) {
        $this->response = $response;
        parent::__construct('', $response->statusCode);
    }
}