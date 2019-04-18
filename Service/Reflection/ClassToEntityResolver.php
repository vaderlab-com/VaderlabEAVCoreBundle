<?php


namespace Vaderlab\EAV\Core\Service\Reflection;


use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Vaderlab\EAV\Core\Entity\AbstractValue;
use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Entity\Entity;
use Vaderlab\EAV\Core\Entity\Schema;
use Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException;
use \ReflectionObject;
use \ReflectionException;
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
     * ClassToEntityResolver constructor.
     * @param RegistryInterface $doctrine
     * @param Reflection $reflection
     * @param EntityServiceORM $entityService
     */
    public function __construct(
        RegistryInterface $doctrine,
        Reflection $reflection,
        EntityServiceORM $entityService
    ) {
        $this->doctrine         = $doctrine;
        $this->reflection       = $reflection;
        $this->entityService    = $entityService;
        $this->schemaRepository = $doctrine->getRepository(Schema::class);
        $this->entityRepository = $doctrine->getRepository(Entity::class);
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
        $entity             = $this->getEntityInstanceByObject($reflectionObject, $entityClass);


        if(!$schema || !($schema instanceof Schema )) {
            throw new ClassToEntityBindException($className);
        }

        $attributes = $schema->getAttributes();

        foreach ($attributes as $attribute) {
            $attrName   = $attribute->getName();
            $value      = $this->reflection->getReflectionAttributeValue($reflectionObject, $entityClass, $attrName);
            $this->entityService->setValue($entity, $attrName, $value);
        }

        return $entity;
    }

    /**
     * @param ReflectionObject $reflectionObject
     * @param object $entityObject
     * @return Entity
     * @throws EntityNotFoundException
     * @throws ReflectionException
     */
    protected function getEntityInstanceByObject(ReflectionObject $reflectionObject, object $entityObject): Entity
    {
        $id = $this->reflection->getReflectionAttributeValue($reflectionObject, $entityObject, Reflection::FOREIGN_PROPERTY);

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