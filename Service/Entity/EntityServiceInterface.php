<?php


namespace Vaderlab\EAV\Core\Service\Entity;


use phpDocumentor\Reflection\Types\String_;
use Vaderlab\EAV\Core\Entity\Entity;
use Vaderlab\EAV\Core\Entity\Schema;

interface EntityServiceInterface
{
    public function createEntity(Schema $schema): Entity;

    public function setValue(Entity $entity, String $attribute, $value);

    public function getValue(Entity $entity, String $attribute);

    public function getValuesArray(Entity $entity): array;
}