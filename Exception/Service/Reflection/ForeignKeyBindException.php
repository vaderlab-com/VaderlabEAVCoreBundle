<?php


namespace Vaderlab\EAV\Core\Exception\Service\Reflection;


use Throwable;

class ForeignKeyBindException extends ReflectionException
{
    public function __construct(string $property, string $classname, $code = 0, Throwable $previous = null)
    {
        $message ='Cannot bind property "%s" for foreign key. Class "%s"';

        parent::__construct(sprintf( $message, $property, $classname ), $code, $previous);
    }
}