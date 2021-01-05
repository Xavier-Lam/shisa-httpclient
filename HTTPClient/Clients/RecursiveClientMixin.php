<?php
namespace Shisa\HTTPClient\Clients;

use Shisa\HTTPClient\Auth\AbstractAuth;

trait RecursiveClientMixin
{
    private $baseClient;

    protected $clients = [];

    public function getBaseClient()
    {
        return $this->baseClient;
    }

    public function setBaseClient($client)
    {
        $this->baseClient = $client;
    }

    public function setAuth(AbstractAuth $auth = null)
    {
        if(method_exists(parent::class, 'auth')) {
            parent::auth();
        }
        foreach($this->clients as $client) {
            if($client instanceof IAuthClient) {
                $client->setAuth($auth);
            }
        }
    }

    /**
     * @return HTTPClient|RecursiveClientMixin
     */
    protected function createChildClient($cls, $args = [])
    {
        $reflectionClass = new \ReflectionClass($cls);
        if($reflectionClass->implementsInterface(IAuthClient::class)) {
            $args = array_merge([$this->getAuth()], $args);
        }
        $client = $reflectionClass->newInstanceArgs($args);
        $client->setBaseUrl($this->getBaseUrl());
        $client->setBaseClient($this->getBaseClient());
        return $client;
    }

    public function __get($name)
    {
        if(!array_key_exists($name, $this->clients)) {
            throw new \InvalidArgumentException('invalid api client: ' . $name);
        }
        if(is_string($this->clients[$name])) {
            $cls = $this->clients[$name];
            $this->clients[$name] = $this->createChildClient($cls);
        }
        return $this->clients[$name];
    }

    public function __call($name, $args)
    {
        if(!array_key_exists($name, $this->clients)) {
            throw new \InvalidArgumentException('invalid api client: ' . $name);
        }
        $key = $name . implode(',', array_map(function($o) {
            if(is_object($o)) {
                return spl_object_hash($o);
            } elseif(is_array($o)) {
                return json_encode($o);
            } else {
                return strval($o);
            }
        }, $args));
        if(!isset($this->clients[$key])) {
            $cls = $this->clients[$name];
            $this->clients[$key] = $this->createChildClient($cls, $args);
        }
        return $this->clients[$key];
    }
}