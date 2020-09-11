<?php
namespace Shisa\HTTPClient\Auth;

use Shisa\HTTPClient\Clients\HTTPClient;
use Shisa\HTTPClient\Exceptions\ResponseError;
use Shisa\HTTPClient\HTTP\Request;

abstract class AbstractAuth
{
    private $client;

    /**
     * @return HTTPClient
     */
    public function getClient()
    {
        return $this->client;
    }

    public function setClient(HTTPClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return false;
    }

    /**
     * @return bool
     */
    abstract function isInvalidAuthError(ResponseError $e);

    abstract function auth();
    
    public function refresh()
    {
        $this->auth();
    }

    /**
     * @return Request
     */
    abstract function authRequest(Request $request);
}