<?php
namespace Shisa\HTTPClient\HTTP;

class PreparedRequest
{
    public $method;

    public $uri;

    public $headers;

    public $data;

    public $request;

    public function __construct($method, $uri, $headers, $data, Request $request = null) {
        $this->method = $method;
        $this->uri = $uri;
        $this->headers = [];
        foreach($headers as $key => $value) {
            $this->headers[] = $key . ': ' . $value;
        }
        $this->data = $data;
        $this->request = $request;
    }

    public function make(Curl $ch = null) {
        $ch = $ch?: Curl::init();
        $ch->setopt(CURLOPT_URL, $this->uri);
        $ch->setopt(CURLOPT_HTTPHEADER, $this->headers);
        $ch->setopt(CURLOPT_CUSTOMREQUEST, $this->method);
        $ch->setopt(CURLOPT_HEADER, 1);
        $ch->setopt(CURLOPT_POSTFIELDS, $this->data);
        return $ch;
    }
}