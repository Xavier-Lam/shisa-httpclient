<?php
namespace Shisa\HTTPClient\Formatters;

class UrlEncodeFormatter implements IFormatter
{
    public function canFormat($data)
    {
        return is_array($data) || is_object($data);
    }

    public function format($data)
    {
        return http_build_query($data);
    }

    public function extendHeaders()
    {
        return [
            'Content-Type' =>  'application/x-www-form-urlencoded'
        ];
    }
}