<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-05
 * Time: 01:26
 */

namespace Vaderlab\EAV\Core\Repository;


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
     * @var DataTypeProvider
     */
    private $dataTypeProvider;

    /**
     * EntityRepository constructor.
     * @param EntityManagerInterface $em
     * @param Mapping\ClassMetadata $class
     * @param DataTypeProvider $dataTypeProvider
     */
    public function __construct(
        EntityManagerInterface $em,
        Mapping\ClassMetadata $class,
        DataTypeProvider $dataTypeProvider
    ) {
        parent::__construct($em, $class);

        $this->dataTypeProvider = $dataTypeProvider;
    }

    /**
     * @param Schema $schema
     * @return Entity
     * @throws \Exception
     */
    public function createEntity(Schema $schema)
    {
        $entity = new Entity();
        $attributes = $schema->getAttributes();
        /** @var Attribute $attribute */
        $entityValues = [];
        foreach ($attributes as $attribute) {
            $valueType = $attribute->getType();
            $valueClass = $this->dataTypeProvider->getValueClass($valueType);

            /** @var AbstractValue $value */
            $value = new $valueClass();
            $value->setEntity($entity);
            $value->setAttribute($attribute);
            $entityValues[] = $value;
        }

        $entity->setValues($entityValues);

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