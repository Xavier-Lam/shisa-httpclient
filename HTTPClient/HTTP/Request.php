<?php
namespace Shisa\HTTPClient\HTTP;

/**
 * @property $data
 * @property $params
 */
class Request
{
    public $method;

    public $scheme;

    public $host;

    public $path;

    private $_data;

    private $_params;

    public $headers;

    public function __construct($url, $method = 'GET', $data = [], $params = [], $headers = []) {
        $this->method = strtoupper($method);

        // 重新处理Url
        // 处理querystring
        $u = parse_url($url);
        $query = [];
        parse_str($u['query'], $query);
        if($this->isNoBodyMethod()) {
            $params = array_merge($params, $data);
            $data = [];
        }
        $params = array_merge($query, $params);

        $this->scheme = $u['scheme'];
        $this->host = $u['host'];
        $this->path = $u['path'];

        $this->_data = $data;
        $this->_params = $params;
        $this->headers = $headers;
    }

    public function prepare($options = []) {
        $uri = $this->prepareUrl($options);
        $headers = $this->prepareHeaders($options, $uri);
        $data = $this->prepareData($options, $headers, $uri);

        return new PreparedRequest($this->method, $uri, $headers, $data);
    }

    protected function prepareUrl($options) {
        $baseUrl = "{$this->scheme}://{$this->host}";
        $uri = "{$this->path}";
        if($this->params) {
            $uri .= '?' . http_build_query($this->params);
        }
        return $baseUrl . $uri;
    }

    protected function prepareHeaders($options, $uri) {
        $headers = $this->headers;
        if(!$this->isNoBodyMethod() && $this->data
            && $options['formatter']->canFormat($this->data)) {
            $headers = array_merge($options['formatter']->extendHeaders(), $headers);
        }
        return $headers;
    }

    protected function prepareData($options, $preparedHeaders, $uri) {
        $data = '';
        if(!$this->isNoBodyMethod() && $this->data) {
            if($options['formatter']->canFormat($this->data)) {
                $data = $options['formatter']->format($this->data);
            } else {
                $data = strval($this->data);
            }
        }
        return $data;
    }

    private function isNoBodyMethod() {
        return in_array($this->method, ['HEAD', 'GET', 'DELETE']);
    }

    public function &__get($name) {
        if($name == 'data') {
            if($this->isNoBodyMethod()) {
                $rv = &$this->_params;
            } else {
                $rv = &$this->_data;
            }
            return $rv;
        }
        if($name == 'params') {
            $rv = &$this->_params;
            return $rv;
        }
        return parent::__get($name);
    }

    public function __set($name, $value) {
        if($name == 'data' && !$this->isNoBodyMethod()) {
            $this->_data = $value;
        }
        parent::__set($name, $value);
    }
}