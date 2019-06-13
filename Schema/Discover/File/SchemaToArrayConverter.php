<?php


namespace Vaderlab\EAV\Core\Schema\Discover\File;


use \Vaderlab\EAV\Core\Schema\Discover\SchemaToArrayConverter as BaseSchemaToArrayConverter;

class SchemaToArrayConverter extends BaseSchemaToArrayConverter
{

    /**
     * {@inheritDoc}
     */
    protected function getName(): string
    {
        return $this->getSchema()['name'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getClassname(): string
    {
        return $this->getSchema()['class'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getAttributes(): array
    {
        return $this->getSchema()['attributes'];
    }
}