<?php namespace Kattatzu\Sbif\Exception;

use Exception;

class InvalidDateException extends Exception
{
    function __construct($date)
    {
        parent::__construct("Invalid or future date ($date)");
    }
}