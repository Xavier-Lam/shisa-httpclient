<?php
namespace Shisa\HTTPClient\Clients;

use Shisa\HTTPClient\Auth\AbstractAuth;
use Shisa\HTTPClient\Exceptions\ResponseError;
use Shisa\HTTPClient\HTTP\Request;
use Shisa\HTTPClient\HTTP\Response;

class AuthClient extends HTTPClient
{
    /**
     * @var AbstractAuth
     */
    private $auth;

    public function __construct(AbstractAuth $auth = null)
    {
        $this->setAuth($auth);
    }

    public function getAuth()
    {
        return $this->auth;
    }

    public function setAuth(AbstractAuth $auth = null)
    {
        $this->auth = $auth;
    }

    public function sendWithAuth($url, $data = [], $method = 'POST', $params = [], $headers = [], $options = [])
    {
        $options['auth'] = true;
        return $this->send($url, $method, $data, $params, $headers, $options);
    }

    public function prepare(Request $request, $options = [])
    {
        if($options['auth']) {
            $auth = $this->getAuth();
            !$auth->isAvailable() && $auth->auth();

            $request = $auth->authRequest($request);
        }
        return parent::prepare($request, $options);
    }

    protected function handleResponse(Response $response, Request $request, $options = [])
    {
        try {
            return parent::handleResponse($response, $request, $options);
        }
        catch(ResponseError $e) {
            if($options['auth'] && $this->getAuth()->isInvalidAuthError($e)) {
                $this->getAuth()->refresh();
                $preparedRequest = $this->prepare($request, $options);
                $response = $this->exec($preparedRequest);
                return parent::handleResponse($response, $request, $options);
            }
            throw $e;
        }
    }
}