<?php


namespace Vaderlab\EAV\Core\Exception\Service\DataType;


use Throwable;

class UnregisteredValueTypeException extends \Exception
{
    public function __construct(string $type, $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Unregistered value type %s', $type);

        parent::__construct($message, $code, $previous);
    }
}