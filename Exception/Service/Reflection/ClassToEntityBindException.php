<?php


namespace Vaderlab\EAV\Core\Exception\Service\Reflection;


use Throwable;

class ClassToEntityBindException extends ReflectionException
{
    public function __construct(string $class, $code = 0, Throwable $previous = null)
    {
        $message = 'Class "%s" cannot bind to EAV Entity.';

        parent::__construct(sprintf($message, $class), $code, $previous);
    }
}