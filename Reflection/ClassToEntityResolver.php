<?php


namespace Vaderlab\EAV\Core\Reflection;


use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Vaderlab\EAV\Core\Annotation\Id;
use Vaderlab\EAV\Core\Entity\AbstractValue;
use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Entity\Entity;
use Vaderlab\EAV\Core\Entity\Schema;
use Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException;
use \ReflectionObject;
use \ReflectionException;
use Vaderlab\EAV\Core\Exception\Service\Reflection\PropertyNotExistsException;
use Vaderlab\EAV\Core\Model\EntityInterface;
use Vaderlab\EAV\Core\Service\Entity\EntityServiceORM;

class ClassToEntityResolver
{
    /**
     * @var RegistryInterface
     */
    private $doctrine;
    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository|\Vaderlab\EAV\Core\Repository\SchemaRepository
     */
    private $schemaRepository;
    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository|\Vaderlab\EAV\Core\Repository\EntityRepository
     */
    private $entityRepository;
    /**
     * @var Reflection
     */
    private $reflection;
    /**
     * @var EntityServiceORM
     */
    private $entityService;

    /**
     * @var EntityClassMetaResolver
     */
    private $metaResolver;

    /**
     * ClassToEntityResolver constructor.
     * @param RegistryInterface $doctrine
     * @param Reflection $reflection
     * @param EntityServiceORM $entityService
     */
    public function __construct(
        RegistryInterface $doctrine,
        Reflection $reflection,
        EntityServiceORM $entityService,
        EntityClassMetaResolver $metaResolver
    ) {
        $this->doctrine         = $doctrine;
        $this->reflection       = $reflection;
        $this->entityService    = $entityService;
        $this->schemaRepository = $doctrine->getRepository(Schema::class);
        $this->entityRepository = $doctrine->getRepository(Entity::class);
        $this->metaResolver     = $metaResolver;
    }

    /**
     * @param object $entityClass
     * @return Entity
     * @throws ClassToEntityBindException
     * @throws EntityNotFoundException
     * @throws ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException
     */
    public function resolve(object $entityClass): Entity
    {
        $className          = get_class($entityClass);

        $schema             = $this->schemaRepository->findOneBy(['entityClass' => $className]);

        $reflectionObject   = $this->reflection->createReflectionObject($entityClass);
        $reflectionClass    = $this->reflection->createReflectionClass($className);
        /** @var array<\Vaderlab\EAV\Core\Annotation\Attribute> $attributes */
        $attributes         = $this->metaResolver->getProtectedAttributes($reflectionClass);
        $entity             = $this->getEntityInstanceByObject($reflectionClass, $reflectionObject, $entityClass);

        if(!$schema || !($schema instanceof Schema )) {
            throw new ClassToEntityBindException($className);
        }

        $entity->setSchema($schema);
        /** @var \Vaderlab\EAV\Core\Annotation\Attribute $attribute */
        foreach ($attributes as $attribute) {
            if($attribute instanceof Id) {
                continue;
            }

            $attrTarget = $attribute->target;
            $attrName   = $attribute->name;
            $value      = $this->reflection->getReflectionAttributeValue($reflectionObject, $entityClass, $attrTarget);

            $this->entityService->setValue($entity, $attrName, $value);
        }

        return $entity;
    }

    /**
     * @param \ReflectionClass $refClass
     * @param ReflectionObject $reflectionObject
     * @param object $entityObject
     * @return Entity
     * @throws EntityNotFoundException
     * @throws PropertyNotExistsException
     * @throws ReflectionException
     */
    protected function getEntityInstanceByObject(
        \ReflectionClass $refClass,
        ReflectionObject $reflectionObject,
        object $entityObject
    ): Entity
    {
        $idProperty = $this->metaResolver->getIdProperty($refClass);

        $id = $this->reflection->getReflectionAttributeValue(
            $reflectionObject,
            $entityObject,
            $idProperty->target
        );

        if(!$id) {
            return new Entity();
        }

        $entity = $this->entityRepository->findOneBy(['id' => $id]);

        if(!$entity) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(Entity::class, [$id]);
        }

        return $entity;
    }
}