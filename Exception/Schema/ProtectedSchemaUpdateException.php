<?php


namespace Vaderlab\EAV\Core\Exception\Schema;


use Throwable;
use Vaderlab\EAV\Core\Model\SchemaInterface;

class ProtectedSchemaUpdateException extends ProtectedSchemaDeniedException
{
    public function __construct(SchemaInterface $schema, $code = 0, Throwable $previous = null)
    {
        $message = sprintf(
                'Updating protected schema "%s" of the model "%s" is prohibited.',
            $schema->getName(),
            $schema->getEntityClass()
        );

        parent::__construct($message, $code, $previous);
    }
}