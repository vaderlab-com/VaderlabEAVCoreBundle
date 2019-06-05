<?php


namespace Vaderlab\EAV\Core\Exception\Service\Reflection;


use Throwable;

class PropertiesAlreadyDeclaredException extends ReflectionException
{
    public function __construct(array $properties, string $class, $code = 0, Throwable $previous = null)
    {
        $message = sprintf(
            'Properties (%s) has already declared on the class "%s"',
            implode(', ', $properties),
            $class
        );

        parent::__construct($message, $code, $previous);
    }
}