<?php


namespace Vaderlab\EAV\Core\Service\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Vaderlab\EAV\Core\Entity\AbstractValue;
use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Entity\Entity;
use Vaderlab\EAV\Core\Entity\Schema;
use Vaderlab\EAV\Core\Entity\ValueTypeHasDefaultInterface;
use Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException;
use Vaderlab\EAV\Core\Reflection\ClassToEntityResolver;
use Vaderlab\EAV\Core\Reflection\EntityToClassResolver;
use Vaderlab\EAV\Core\Repository\EntityRepository;
use Vaderlab\EAV\Core\ORM\DataType\DataTypeProvider;

/**
 * Class EntityServiceORM
 * @package Vaderlab\EAV\Core\Service\Entity
 *
 * @todo: Exception
 */
class EAVEntityManagerORM implements \Vaderlab\EAV\Core\Service\Entity\EAVEntityManagerInterface
{
    /**
     * @var DataTypeProvider
     */
    private $dataTypeProvider;
    /**
     * @var EAVEntityManagerInterface
     */
    private $entityManager;
    /**
     * @var String
     */
    private $entityClass;

    /**
     * @var ClassToEntityResolver
     */
    private $classToEntityResolver;

    /**
     * @var EntityToClassResolver
     */
    private $entityToClassResolver;

    /**
     * EntityServiceORM constructor.
     * @param EntityManagerInterface $entityManager
     * @param DataTypeProvider $dataTypeProvider
     * @param ClassToEntityResolver $classToEntityResolver
     * @param String $entityClass
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        DataTypeProvider $dataTypeProvider,
        ClassToEntityResolver $classToEntityResolver,
        String $entityClass
    ) {
        $this->entityManager         = $entityManager;
        $this->dataTypeProvider      = $dataTypeProvider;
        $this->classToEntityResolver = $classToEntityResolver;
        $this->entityClass           = $entityClass;
    }

    /**
     * Create entity object
     *
     * @param Schema $schema
     * @return Entity
     * @throws \Exception
     */
    public function createEntity(?Schema $schema): Entity
    {
        $entity         = new Entity();
        $entity->setSchema($schema);

        $attributes     = $schema->getAttributes();
        /** @var Attribute $attribute */
        $entityValues   = new ArrayCollection();

        $entity->setSchema($schema);

        foreach ($attributes as $attribute) {
            $entityValue = $this
                ->initEntityValueAttributeByName($schema, $entity, $attribute->getName());
            $entityValues->add($entityValue);
        }

        $entity->setValues($entityValues);

        return $entity;
    }

    /**
     * @param string $classname
     * @param int $id
     * @return object|null
     */
    public function findByClassAndId(string $classname, int $id)
    {
        $qb = $this->getEAVEntityRepository()->createQueryBuilder('q');
        $qb
            ->innerJoin('q.schema', 's')
            ->andWhere('q.id = :id')
            ->andWhere('s.entityClass = :class OR s.name = :class')
            ->setParameters([
                'class' => $classname,
                'id'    => $id,
            ])
        ;
        $result = $qb->getQuery()->getResult();

        return count($result) ? $result[0] : null;
    }

    /**
     * @param int $id
     * @return Entity|null
     */
    public function findById(int $id): ?Entity
    {
        return $this->getEAVEntityRepository()->findOneBy(['id' => $id]);
    }

    /**
     * @param $entity
     * @param String $attribute
     * @return mixed
     * @throws UnregisteredEntityAttributeException
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     */
    public function getValue($entity, String $attribute)
    {
        $entity = $this->resolveEntity($entity);
        $attributeObj = $this->getEntityValueObjByName($entity, $attribute);

        return $attributeObj->getValue();
    }

    /**
     * @param $entity
     * @return array
     * @throws UnregisteredEntityAttributeException
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     */
    public function getValuesArray($entity): array
    {
        $entity = $this->resolveEntity($entity);
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
     * @param $entity
     * @param String $attribute
     * @param $value
     * @return Entity
     * @throws UnregisteredEntityAttributeException
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     */
    public function setValue($entity, String $attribute, $value): Entity
    {
        $entity = $this->resolveEntity($entity);
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
    public function getEntityValueObjByName($entity, string $attributeName): AbstractValue
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
     * @param null $entity
     * @return Entity|null
     * @throws UnregisteredEntityAttributeException
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ForeignPropertyException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertyNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException
     */
    public function resolveEAVEntity($entity = null): ?Entity
    {
        if($entity === null) {
            return null;
        }

        if($entity instanceof Entity) {
            return $entity;
        }

        return $this->classToEntityResolver->resolve($entity);
    }

    /**
     * @param string $classname
     * @return bool
     */
    public function isEavEntityClass(string $classname): bool
    {
        $repository = $this->getEAVEntityRepository();
        $qb         = $repository->createQueryBuilder('q');

        $qb ->select('q.id')
            ->innerJoin('q.schema', 's')
            ->where('s.name = :name OR s.entityClass = :name')
            ->setParameter('name', $classname)
        ;

        $result = $qb->getQuery()->getArrayResult();

        return !!count($result);
    }

    /**
     * @param object $object
     * @return bool
     */
    public function isEAVEntity(object $object): bool
    {
        return $this->isEavEntityClass(get_class($object));
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository|\Vaderlab\EAV\Core\Repository\EntityRepository
     */
    public function getEAVEntityRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Entity::class);
    }

    /**
     * @param Schema $schema
     * @param Entity $entity
     * @param string $attributeName
     * @return AbstractValue
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     */
    protected function initEntityValueAttributeByName(
        Schema $schema,
        Entity $entity,
        string $attributeName): AbstractValue
    {
        $attributeObject    = $schema->getAttribute($attributeName);
        $valueObject        = $this->dataTypeProvider->createValueObject($attributeObject->getType());

        $valueObject->setEntity($entity);
        $entity->getValues()->add($valueObject);

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

    /**
     *
     * @todo: create  exception
     *
     * @param object $object
     * @return Entity|null
     * @throws UnregisteredEntityAttributeException
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ForeignPropertyException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertyNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException
     */
    protected function resolveEntity(object $object): ?Entity
    {
        if($object instanceof Entity) {
            return $object;
        }

        if(!$this->isEavEntity($object)) {
            throw new \LogicException('Object is not Entity');
        }

        return $this->classToEntityResolver->resolve($object);
    }
}