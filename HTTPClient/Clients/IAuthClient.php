<?php
namespace Shisa\HTTPClient\Clients;

use Shisa\HTTPClient\Auth\AbstractAuth;

interface IAuthClient
{
    function getAuth(): ?AbstractAuth;

    function setAuth(AbstractAuth $auth = null);

    function sendWithAuth($url, $data = [], $method = 'GET', $params = [], $headers = [], $options = []);
}