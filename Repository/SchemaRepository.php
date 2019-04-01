<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-05
 * Time: 01:40
 */

namespace Vaderlab\EAV\Core\Repository;


use ArrayAccess;
use Doctrine\ORM\EntityRepository;
use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Entity\Schema;

class SchemaRepository extends EntityRepository
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