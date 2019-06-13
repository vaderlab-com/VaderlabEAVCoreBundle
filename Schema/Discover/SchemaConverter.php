<?php


namespace Vaderlab\EAV\Core\Schema\Discover;


class SchemaConverter
{
    private $schemaToArrayConverter;

    private $schemaDiscover;

    public function __construct(
        SchemaDiscoverInterface $schemaDiscover,
        SchemaToArrayConverter $converter
    ) {
        $this->schemaToArrayConverter = $converter;
        $this->schemaDiscover = $schemaDiscover;
    }

    public function convert(): array
    {
        $schema = $this->schemaDiscover->getSchema();
    }
}