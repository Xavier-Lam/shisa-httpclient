<?php
namespace Shisa\HTTPClient\Formatters;

interface IFormatter
{
    /**
     * @return bool
     */
    function canFormat($data);

    /**
     * @return string
     */
    function format($data);

    /**
     * @return array
     */
    function extendHeaders();
}