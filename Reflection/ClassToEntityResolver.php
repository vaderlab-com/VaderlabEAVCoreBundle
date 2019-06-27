<?php


namespace Vaderlab\EAV\Core\Reflection;


use Doctrine\ORM\EntityNotFoundException;
use ReflectionClass;
use Vaderlab\EAV\Core\Annotation\Attribute;
use Vaderlab\EAV\Core\Annotation\Id;
use Vaderlab\EAV\Core\Entity\Entity;
use Vaderlab\EAV\Core\Entity\Schema;
use Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException;
use Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException;
use Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException;
use \ReflectionObject;
use \ReflectionException;
use Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException;
use Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException;
use Vaderlab\EAV\Core\Exception\Service\Reflection\ForeignPropertyException;
use Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException;
use Vaderlab\EAV\Core\Exception\Service\Reflection\PropertyNotExistsException;
use Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException;
use Vaderlab\EAV\Core\Service\Entity\EAVEntityManagerInterface;
use Vaderlab\EAV\Core\Service\Entity\EAVEntityManagerORM;
use Vaderlab\EAV\Core\Service\Entity\EntityServiceProxy;
use Vaderlab\EAV\Core\Service\Schema\EAVSchemaManagerInterface;

class ClassToEntityResolver
{
    /**
     * @var Reflection
     */
    private $reflection;
    /**
     * @var EAVEntityManagerORM
     */
    private $entityService;

    /**
     * @var EntityServiceProxy
     */
    private $entityServiceProxy;

    /**
     * @var EntityClassMetaResolver
     */
    private $metaResolver;

    /**
     * @var EAVSchemaManagerInterface
     */
    private $schemaManager;

    /**
     * @var EAVEntityManagerInterface|null
     */
    private $entityManager;

    /**
     * ClassToEntityResolver constructor.
     * @param Reflection $reflection
     * @param EntityServiceProxy $entityServiceProxy
     * @param EntityClassMetaResolver $metaResolver
     * @param EAVSchemaManagerInterface $schemaManager
     */
    public function __construct(
        Reflection $reflection,
        EntityServiceProxy $entityServiceProxy,
        EntityClassMetaResolver $metaResolver,
        EAVSchemaManagerInterface $schemaManager
    ) {
        $this->reflection       = $reflection;
        $this->entityServiceProxy    = $entityServiceProxy;
        $this->metaResolver     = $metaResolver;
        $this->schemaManager    = $schemaManager;
    }

    /**
     * @param object $entityClass
     * @return Entity
     * @throws ClassToEntityBindException
     * @throws EntityNotFoundException
     * @throws PropertyNotExistsException
     * @throws ReflectionException
     * @throws UnregisteredValueTypeException
     * @throws UnregisteredEntityAttributeException
     * @throws EntityClassBindException
     * @throws EntityClassNotExistsException
     * @throws ForeignPropertyException
     * @throws PropertiesAlreadyDeclaredException
     * @throws PropertySchemeInvalidException
     */
    public function resolve(object $entityClass): Entity
    {
        $className          = get_class($entityClass);
        $schema             = $this->schemaManager->findByClass($className);
        $entityService      = $this->getEntityService();
        $reflectionObject   = $this->reflection->createReflectionObject($entityClass);
        $reflectionClass    = $this->reflection->createReflectionClass($className);
        /** @var array<\Vaderlab\EAV\Core\Annotation\Attribute> $attributes */
        $attributes         = $this->metaResolver->getProtectedAttributes($reflectionClass);

        if(!$schema || !($schema instanceof Schema )) {
            throw new ClassToEntityBindException($className);
        }

        $entity             = $this->getEntityInstanceByObject($reflectionClass, $reflectionObject, $entityClass, $schema);

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            if($attribute instanceof Id) {
                continue;
            }

            $attrTarget = $attribute->target;
            $attrName   = $attribute->name;
            $value      = $this->reflection->getReflectionAttributeValue($reflectionObject, $entityClass, $attrTarget);

            $entityService->setValue($entity, $attrName, $value);
        }

        return $entity;
    }

    /**
     * @return EAVEntityManagerInterface|EAVEntityManagerORM
     */
    protected function getEntityService()
    {
        if(!$this->entityService) {
            return $this->entityService = $this->entityServiceProxy->getService();
        }

        return $this->entityService;
    }

    /**
     * @param ReflectionClass $refClass
     * @param  ReflectionObject $reflectionObject
     * @param  object $entityObject
     * @param  Schema $schema
     * @return Entity
     * @throws EntityNotFoundException
     * @throws PropertyNotExistsException
     * @throws ReflectionException
     * @throws ForeignPropertyException
     * @throws PropertySchemeInvalidException
     */
    protected function getEntityInstanceByObject(
        ReflectionClass $refClass,
        ReflectionObject $reflectionObject,
        object $entityObject,
        Schema $schema
    ): Entity
    {
        $idProperty = $this->metaResolver->getIdProperty($refClass);

        $entityService = $this->getEntityService();
        $id = $this->reflection->getReflectionAttributeValue(
            $reflectionObject,
            $entityObject,
            $idProperty->target
        );

        if(!$id) {
            return $entityService->createEntity($schema);
        }

        $entity = $entityService->findById($id);

        if(!$entity) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(Entity::class, [$id]);
        }

        return $entity;
    }
}