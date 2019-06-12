<?php


namespace Vaderlab\EAV\Core\Schema\Discover;


interface SchemaConverterInterface
{
    public function convert(array $schema): array;
}