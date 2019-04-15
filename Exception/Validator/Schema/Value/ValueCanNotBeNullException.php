<?php


namespace Vaderlab\EAV\Core\Exception\Validator\Schema\Value;


use Throwable;

class ValueCanNotBeNullException extends InvalidValueException
{
    public function __construct(string $attribute, $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Attribute "%s" can not be NULL.', $attribute);

        parent::__construct($message, $code, $previous);
    }
}