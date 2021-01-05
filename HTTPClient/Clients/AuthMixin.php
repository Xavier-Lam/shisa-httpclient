<?php
namespace Shisa\HTTPClient\Clients;

use Shisa\HTTPClient\Auth\AbstractAuth;
use Shisa\HTTPClient\HTTP\Request;
use Shisa\HTTPClient\HTTP\Response;

trait AuthMixin
{
    /**
     * @var AbstractAuth
     */
    private $auth;

    public function __construct(AbstractAuth $auth = null)
    {
        parent::__construct();
        $this->setAuth($auth);
    }

    public function getAuth(): ?AbstractAuth
    {
        return $this->auth;
    }

    public function setAuth(AbstractAuth $auth = null)
    {
        $this->auth = $auth;
    }

    public function sendWithAuth($url, $data = [], $method = 'GET', $params = [], $headers = [], $options = [])
    {
        $options['auth'] = true;
        return $this->send($url, $method, $data, $params, $headers, $options);
    }

    public function prepare(Request $request, $options = [])
    {
        $auth = $options['auth'];
        if($auth) {
            $auth = $auth instanceof AbstractAuth?
                $auth:
                $this->getAuth();
            !$auth->isAvailable() && $auth->auth();

            $request = $auth->authRequest($request);
        }
        $preparedRequest = parent::prepare($request, $options);
        if($auth) {
            $preparedRequest = $auth->authRequestPostPrepare($preparedRequest, $request);
        }
        return $preparedRequest;
    }

    protected function handleResponse(Response $response, Request $request, $options = [])
    {
        try {
            return parent::handleResponse($response, $request, $options);
        }
        catch(\Exception $e) {
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