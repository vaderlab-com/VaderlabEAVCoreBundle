<?php


namespace Vaderlab\EAV\Core\Service\Entity;


use phpDocumentor\Reflection\Types\String_;
use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Entity\Entity;
use Vaderlab\EAV\Core\Entity\Schema;

interface EAVEntityManagerInterface
{
    /**
     * @param string $classname
     * @return bool
     */
    public function isEAVEntityClass(string $classname): bool;

    /**
     * @param object $object
     * @return bool
     */
    public function isEAVEntity(object $object): bool;

    /**
     * @param int $id
     * @return Entity|null
     */
    public function findById(int $id): ?Entity;

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
     * @return array
     */
    public function getValuesArray($entity): array;
}