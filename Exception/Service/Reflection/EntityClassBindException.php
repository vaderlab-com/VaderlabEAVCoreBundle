<?php


namespace Vaderlab\EAV\Core\Exception\Service\Reflection;


use Throwable;

class EntityClassBindException extends ReflectionException
{
    public function __construct($class = "", $code = 0, Throwable $previous = null)
    {
        $message = 'Entity class "%s" binding error';

        parent::__construct(sprintf($message, $class), $code, $previous);
    }
}