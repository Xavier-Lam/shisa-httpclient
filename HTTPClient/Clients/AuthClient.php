<?php
namespace Shisa\HTTPClient\Clients;

class AuthClient extends HTTPClient implements IAuthClient
{
    use AuthMixin;
}