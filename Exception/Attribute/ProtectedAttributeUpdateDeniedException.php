<?php


namespace Vaderlab\EAV\Core\Exception\Attribute;


use Throwable;
use Vaderlab\EAV\Core\Model\AttributeInterface;
use Vaderlab\EAV\Core\Model\SchemaInterface;

class ProtectedAttributeUpdateDeniedException extends ProtectedAttributeDeniedException
{
    public function __construct(AttributeInterface $attribute, SchemaInterface $schema, $code = 0, Throwable $previous = null)
    {
        $message = sprintf(
            'Updating protected attribute "%s" of the model "%s" is prohibited.',
            $attribute->getName(),
            $schema->getName()
        );

        parent::__construct($message, $code, $previous);
    }
}