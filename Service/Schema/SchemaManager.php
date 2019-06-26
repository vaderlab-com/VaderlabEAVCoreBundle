<?php


namespace Vaderlab\EAV\Core\Service\Schema;


use ArrayAccess;
use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Entity\Schema;

class SchemaManager
{
    /**
     * @param String $name
     * @param ArrayAccess $attributes
     * @return Schema
     */
    public function createSchema(String $name, ArrayAccess $attributes)
    {
        $schema = new Schema();
        $schema->setName($name);
        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            $attribute->setSchema($schema);
        }

        $schema->setAttributes($attributes);

        return $schema;
    }
}