<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-05
 * Time: 01:26
 */

namespace Vaderlab\EAV\Core\Repository;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\Query\Expr\Join;
use Vaderlab\EAV\Core\Entity\AbstractValue;
use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Entity\EAVEntityInterface;
use Vaderlab\EAV\Core\Entity\Entity;
use Vaderlab\EAV\Core\Entity\Schema;
use Vaderlab\EAV\Core\Entity\ValueTypeHasDefaultInterface;
use Vaderlab\EAV\Core\Service\DataType\DataTypeProvider;

/**
 * Class EntityRepository
 * @package Vaderlab\EAV\Core\Repository
 *
 */
class EntityRepository extends BaseEntityRepository
{
    /**
     * @param Schema $schema
     * @return Entity
     * @throws \Exception
     */
    public function createEntity(Schema $schema): Entity
    {
        $entity         = new Entity();

        return $entity;
    }

    /**
     * @param EAVEntityInterface $entity
     * @param String $attributeName
     * @param bool $allowUnregistered
     * @return null|AbstractValue|ValueTypeHasDefaultInterface
     * @throws \Exception
     */
    public function getAttributeValueData(
        EAVEntityInterface $entity,
        String $attributeName,
        bool $allowUnregistered = true
    ) {
        $attribute = $this->getAttribute($entity, $attributeName, $allowUnregistered);
        if(!$attribute) {
            return null;
        }

        return $attribute->getValue();
    }

    /**
     * @param EAVEntityInterface $entity
     * @param String $attributeName
     * @param bool $allowUnregistered
     * @return mixed|null
     * @throws \Exception
     */
    public function getAttributeValue(
        EAVEntityInterface $entity,
        String $attributeName,
        bool $allowUnregistered = true
    ) {
        $value = $this->getAttributeValueData($entity, $attributeName, $allowUnregistered);
        if(!$value) {
            return null;
        }

        $result = $value->getValue();
        if($result) {
            return $result;
        }

        if($value instanceof ValueTypeHasDefaultInterface) {
            return $value->getDefaultValue();
        }

        return null;
    }

    /**
     * @param EAVEntityInterface $entity
     * @param String $attributeName
     * @param bool $allowUnregistered
     * @return mixed|Attribute|null
     * @throws \Exception
     */
    public function getAttribute(
        EAVEntityInterface $entity,
        String $attributeName,
        bool $allowUnregistered = true
    ) {
        $attributes = $this->getAttributes($entity);
        /** @var Attribute $tmpAttribute */
        foreach ($attributes as $tmpAttribute) {
            if( $tmpAttribute->getName() === $attributeName ) {
                return $tmpAttribute;
            }
        }

        if($allowUnregistered === true) {
            return null;
        }

        throw new \Exception(
            sprintf('Undefined attribute %s', $attributeName)
        );
    }

    /**
     * @param EAVEntityInterface $entity
     * @return mixed
     */
    public function getAttributes(EAVEntityInterface $entity) {
        $values = $entity->getValues();

        $attributes = [];
        /** @var AbstractValue $value */
        foreach ($values as $value) {
            $attribute = $value->getAttribute();
            $attributes[] = $attribute;
        }

        return $attributes;
    }
}