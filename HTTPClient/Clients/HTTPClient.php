<?php
namespace Shisa\HTTPClient\Clients;

use Shisa\HTTPClient\Exceptions\CurlError;
use Shisa\HTTPClient\Exceptions\HTTPError;
use Shisa\HTTPClient\Formatters\IFormatter;
use Shisa\HTTPClient\Formatters\JsonFormatter;
use Shisa\HTTPClient\HTTP\PreparedRequest;
use Shisa\HTTPClient\HTTP\Request;
use Shisa\HTTPClient\HTTP\Response;

class HTTPClient
{
    /**
     * 响应失败不抛出
     */
    const OPTION_RESPONSENOEXCEPTIONS = 'nothrow';

    protected $baseUrl;

    /**
     * @param IFormatter
     */
    private $formatter;

    public function __construct()
    {
        
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function setFormatter(IFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public function getFormatter()
    {
        if(!$this->formatter) {
            $this->setFormatter(new JsonFormatter());
        }
        return $this->formatter;
    }
    
    /**
     * @return Response
     */
    public function send($url, $method = 'GET', $data = [], $params = [], $headers = [], $options = [])
    {
        if(is_a($url, PreparedRequest::class)) {
            $preparedRequest = $url;
        } else {
            $preparedRequest = $this->createPrepareRequest($url, $method, $data, $params, $headers, $options);
        }
        $response = $this->exec($preparedRequest);
        return $this->handleResponse($response, $preparedRequest->request, $options);
    }

    protected function getRequestClass()
    {
        return Request::class;
    }

    /**
     * @return Request
     */
    public function createRequest($url, $method = 'GET', $data = [], $params = [], $headers = [], $options = [])
    {
        if(!parse_url($url, PHP_URL_HOST)) {
            $url = $this->getBaseUrl() . $url;
        }
        $cls = $this->getRequestClass();
        return new $cls($url, $method, $data, $params, $headers);
    }

    public function createPrepareRequest($url, $method, $data, $params, $headers, $options)
    {
        if(is_a($url, Request::class)) {
            $request = $url;
        } else {
            $request = $this->createRequest($url, $method, $data, $params, $headers, $options);
        }
        return $this->prepare($request, $options);
    }

    /**
     * @return PreparedRequest
     */
    public function prepare(Request $request, $options = [])
    {
        if(!isset($options['formatter'])) {
            $options['formatter'] = $this->getFormatter();
        }
        return $request->prepare($options);
    }

    /**
     * @return Response
     */
    public final function exec(PreparedRequest $preparedRequest)
    {
        $ch = $preparedRequest->make();
        $resp = $ch->exec();
        return new Response($ch, $resp);
    }

    protected function handleResponse(Response $response, Request $request, $options = [])
    {
        if($response->errno) {
            throw new CurlError($response->error, $response->errno);
        }

        if(!$options[static::OPTION_RESPONSENOEXCEPTIONS] && !$response->isSuccess()) {
            throw new HttpError($response);
        }
        
        return $response;
    }
}