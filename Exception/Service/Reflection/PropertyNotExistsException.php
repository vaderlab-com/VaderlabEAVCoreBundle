<?php


namespace Vaderlab\EAV\Core\Exception\Service\Reflection;


use Throwable;

class PropertyNotExistsException extends ReflectionException
{
    public function __construct(string $property, string $classname, $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Property "%s" is not exists on the class "%s"', $property, $classname);

        parent::__construct($message, $code, $previous);
    }
}