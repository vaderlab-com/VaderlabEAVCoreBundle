<?php


namespace Vaderlab\EAV\Core\Exception\Validator\Schema\Value;


use Throwable;

class InvalidValueException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}