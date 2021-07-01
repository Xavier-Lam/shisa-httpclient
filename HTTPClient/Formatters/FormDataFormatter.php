<?php
namespace Shisa\HTTPClient\Formatters;

class FormDataFormatter implements IFormatter
{
    public function canFormat($data)
    {
        return is_array($data) || is_object($data);
    }

    public function format($data)
    {
        return $data;
    }

    public function extendHeaders()
    {
        return [
            'Content-Type' =>  'multipart/form-data'
        ];
    }
}