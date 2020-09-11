<?php
namespace Shisa\HTTPClient\Formatters;

class JsonFormatter implements IFormatter
{
    protected $options;

    public function __construct($options = 0)
    {
        $this->options = $options;
    }

    public function canFormat($data)
    {
        return is_array($data) || is_object($data);
    }

    public function format($data)
    {
        return json_encode($data, $this->options);
    }

    public function extendHeaders()
    {
        return [
            'Content-Type' =>  'application/json'
        ];
    }
}