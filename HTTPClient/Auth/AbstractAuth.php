<?php
namespace Shisa\HTTPClient\Auth;

use Exception;
use Shisa\HTTPClient\Clients\HTTPClient;
use Shisa\HTTPClient\HTTP\PreparedRequest;
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
     * @param \Exception $e
     * @return bool
     */
    abstract function isInvalidAuthError($e);

    abstract function auth();
    
    public function refresh()
    {
        $this->auth();
    }

    public function authRequest(Request $request)
    {
        return $request;
    }

    public function authRequestPostPrepare(PreparedRequest $preparedRequest, Request $request)
    {
        return $preparedRequest;
    }
}