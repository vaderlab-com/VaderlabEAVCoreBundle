<?php


namespace Vaderlab\EAV\Core\Service\Entity;


use phpDocumentor\Reflection\Types\String_;
use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Entity\Entity;
use Vaderlab\EAV\Core\Entity\Schema;

interface EntityServiceInterface
{
    /**
     * @param Schema $schema
     * @return Entity
     */
    public function createEntity(Schema $schema): Entity;

    /**
     * @param $entity
     * @param String $attribute
     * @param $value
     * @return mixed
     */
    public function setValue($entity, String $attribute, $value);

    /**
     * @param $entity
     * @param String $attribute
     * @return mixed
     */
    public function getValue($entity, String $attribute);

    /**
     * @param $entity
     * @param Attribute $attribute
     * @return mixed
     */
    public function getValueByAttribute($entity, Attribute $attribute);

    /**
     * @param $entity
     * @return array
     */
    public function getValuesArray($entity): array;
}