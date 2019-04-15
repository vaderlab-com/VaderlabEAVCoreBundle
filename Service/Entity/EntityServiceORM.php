<?php


namespace Vaderlab\EAV\Core\Service\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Vaderlab\EAV\Core\Entity\AbstractValue;
use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Entity\Entity;
use Vaderlab\EAV\Core\Entity\Schema;
use Vaderlab\EAV\Core\Entity\ValueTypeHasDefaultInterface;
use Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException;
use Vaderlab\EAV\Core\Repository\EntityRepository;
use Vaderlab\EAV\Core\Service\DataType\DataTypeProvider;

class EntityServiceORM implements EntityServiceInterface
{
    /**
     * @var DataTypeProvider
     */
    private $dataTypeProvider;
    /**
     * @var EntityRepository
     */
    private $repository;
    /**
     * @var RegistryInterface
     */
    private $doctrine;
    /**
     * @var String
     */
    private $entityClass;

    /**
     * EntityService constructor.
     * @param RegistryInterface $doctrine
     * @param DataTypeProvider $dataTypeProvider
     * @param String $entityClass
     */
    public function __construct(
        RegistryInterface $doctrine,
        DataTypeProvider $dataTypeProvider,
        String $entityClass
    ) {
        $this->doctrine             = $doctrine;
        $this->dataTypeProvider     = $dataTypeProvider;
        $this->repository           = $doctrine->getRepository($entityClass);
        $this->entityClass          = $entityClass;
    }

    /**
     * Create entity object
     *
     * @param Schema $schema
     * @return Entity
     * @throws \Exception
     */
    public function createEntity(Schema $schema): Entity
    {
        $entity = $this->repository->createEntity($schema);

        $attributes     = $schema->getAttributes();
        /** @var Attribute $attribute */
        $entityValues   = new ArrayCollection();

        $entity->setSchema($schema);

        foreach ($attributes as $attribute) {
            $entityValue = $this->initEntityValueAttributeByName($schema, $entity, $attribute->getName());
            $entityValues->add($entityValue);
        }

        $entity->setValues($entityValues);

        return $entity;
    }

    /**
     * @param Entity $entity
     * @param String $attribute
     * @return mixed
     * @throws UnregisteredEntityAttributeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     */
    public function getValue(Entity $entity, String $attribute)
    {
        $attributeObj = $this->getEntityValueObjByName($entity, $attribute);

        return $attributeObj->getValue();
    }

    /**
     * @param Entity $entity
     * @return array
     */
    public function getValuesArray(Entity $entity): array
    {
        $result = [];
        $values = $entity->getValues();
        /** @var AbstractValue $value */
        foreach ($values as $value) {
            $attribute                  = $value->getAttribute();
            $attributeTitle             = $attribute->getName();
            $result[$attributeTitle]    = $value->getValue();
        }

        return $result;
    }

    /**
     * @param Entity $entity
     * @param String $attribute
     * @param $value
     * @return Entity
     * @throws UnregisteredEntityAttributeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     */
    public function setValue(Entity $entity, String $attribute, $value): Entity
    {
        $attributeObj = $this->getEntityValueObjByName($entity, $attribute);
        $attributeObj->setValue($value);

        return $entity;
    }

    /**
     * @param Entity $entity
     * @param string $attributeName
     * @return AbstractValue
     * @throws UnregisteredEntityAttributeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     */
    protected function getEntityValueObjByName(Entity $entity, string $attributeName): AbstractValue
    {
        $schema = $entity->getSchema();

        if (!$schema->hasAttribute($attributeName)) {
            throw new UnregisteredEntityAttributeException($attributeName);
        }

        $attributes = $entity->getValues()->filter(function (AbstractValue $value) use ($attributeName) {
            $attribute = $value->getAttribute();

            return $attribute->getName() === $attributeName;
        });

        if ($attributes->count()) {
            return $attributes->first();
        }

        return $this->initEntityValueAttributeByName($schema, $entity, $attributeName);
    }

    /**
     * @param Schema $schema
     * @param Entity $entity
     * @param string $attributeName
     * @return AbstractValue
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     */
    protected function initEntityValueAttributeByName(Schema $schema, Entity $entity, string $attributeName): AbstractValue
    {
        $attributeObject    = $schema->getAttribute($attributeName);
        $valueObject        = $this->dataTypeProvider->createValueObject($attributeObject->getType());

        $valueObject->setEntity($entity);
        $valueObject->setAttribute($attributeObject);

        if (!($valueObject instanceof ValueTypeHasDefaultInterface)) {
            return $valueObject;
        }

        $defaultValue = $attributeObject->getDefaultValue();
        if (!$defaultValue) {
            return $valueObject;
        }

        settype($defaultValue, $valueObject->getCastType());
        $valueObject->setValue($defaultValue);

        return $valueObject;
    }
}