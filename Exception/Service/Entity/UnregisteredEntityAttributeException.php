<?php


namespace Vaderlab\EAV\Core\Exception\Service\Entity;


use Throwable;

class UnregisteredEntityAttributeException extends \Exception
{
    public function __construct(String $attribute, $code = 0, Throwable $previous = null)
    {
        $message = 'Unregistered attribute "%s"';
        $message = sprintf($message, $attribute);

        parent::__construct($message, $code, $previous);
    }
}