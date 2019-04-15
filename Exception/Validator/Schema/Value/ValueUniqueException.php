<?php


namespace Vaderlab\EAV\Core\Exception\Validator\Schema\Value;


use Throwable;
use Vaderlab\EAV\Core\Entity\AbstractValue;
use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Entity\Schema;

class ValueUniqueException extends InvalidValueException
{
    public function __construct(
        Schema $schema,
        Attribute $attribute,
        AbstractValue $value,
        $code = 0,
        Throwable $previous = null
    ) {
        $message = 'Attribute "%s" value "%s" already exists. Schema [%d] "%s".';
        $message = sprintf(
            $message,
            $attribute->getName(),
            (string)$value->getValue(),
            $schema->getId(),
            $schema->getName()
        );

        parent::__construct($message, $code, $previous);
    }
}