<?php


namespace Vaderlab\EAV\Core\Schema\Discover\ORM;


use Vaderlab\EAV\Core\Entity\Schema;
use \Vaderlab\EAV\Core\Schema\Discover\SchemaToArrayConverter as BaseSchemaToArrayConverter;

class SchemaToArrayConverter extends BaseSchemaToArrayConverter
{

    /**
     * @return Schema
     */
    public function getSchema()
    {
        return parent::getSchema();
    }

    /**
     * {@inheritDoc}
     */
    protected function getName(): string
    {
        return $this->getSchema()->getName();
    }

    /**
     * {@inheritDoc}
     */
    protected function getClassname(): string
    {
        return $this->getSchema()->getEntityClass();
    }

    /**
     * {@inheritDoc}
     */
    protected function getAttributes(): array
    {
        return $this->getSchema()->getAttributes()->toArray();
    }
}