<?php


namespace Vaderlab\EAV\Core\Service\Reflection;


use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Entity\Entity;
use \ReflectionObject;
use \ReflectionException;
use Vaderlab\EAV\Core\Exception\Service\Reflection\ForeignKeyBindException;
use Vaderlab\EAV\Core\Service\Entity\EntityServiceInterface;

/**
 * Class EntityObjectResolver
 * @package Vaderlab\EAV\Core\Service\Reflection
 */
class EntityObjectResolver
{
    /**
     * @var EntityServiceInterface
     */
    private $entityService;

    /**
     * @var Reflection
     */
    private $reflectionService;

    /**
     * EntityObjectResolver constructor.
     * @param EntityServiceInterface $entityService
     * @param Reflection $reflection
     */
    public function __construct(
        EntityServiceInterface $entityService,
        Reflection $reflection
    ) {
        $this->entityService = $entityService;
        $this->reflectionService = $reflection;
    }

    /**
     * @param Entity $entity
     * @return object
     * @throws ForeignKeyBindException
     * @throws ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     */
    public function resolve(Entity $entity): object
    {
        $schema         = $entity->getSchema();
        $entityClass    = $schema->getEntityClass();

        if(!$entityClass) {
            return $entity;
        }

        $attributes     = $schema->getAttributes();
        $entityObject   = $this->reflectionService->createObject($entityClass);
        $entityRef      = $this->reflectionService->createReflectionObject($entityObject);

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            $this->updateEntityObjectAttribute(
                $entity,
                $attribute,
                $entityRef,
                $entityObject
            );
        }

        $this->updateEntityObjectByAttributeName(
            Reflection::FOREIGN_PROPERTY,
            $entityRef,
            $entityObject,
            $entity->getId(),
            true
        );

        return $entityObject;
    }

    /**
     * @param Entity $entity
     * @param Attribute $attribute
     * @param ReflectionObject $reflectionObject
     * @param object $entityObject
     * @throws ForeignKeyBindException
     * @throws ReflectionException
     */
    protected function updateEntityObjectAttribute(
        Entity $entity,
        Attribute $attribute,
        ReflectionObject $reflectionObject,
        object $entityObject
    ): void
    {
        $attrName = $attribute->getName();
        $currentValue = $this->entityService->getValue($entity, $attrName);
        $this->updateEntityObjectByAttributeName(
            $attrName,
            $reflectionObject,
            $entityObject,
            $currentValue,
            false
        );
    }

    /**
     * @param Entity $entity
     * @param string $attribute
     * @param ReflectionObject $reflectionObject
     * @param object $entityObject
     * @param null $value
     * @param bool $strict
     * @throws ForeignKeyBindException
     * @throws ReflectionException
     */
    protected function updateEntityObjectByAttributeName(
        string $attribute,
        ReflectionObject $reflectionObject,
        object $entityObject,
        $value = null,
        bool $strict = true
    ): void
    {
        if(!$reflectionObject->hasProperty($attribute) && $strict) {
            throw new ForeignKeyBindException($attribute, get_class($entityObject));
        }

        $property = $reflectionObject->getProperty($attribute);

        if(!$property->isPublic()) {
            $property->setAccessible(true);
        }

        $property->setValue($entityObject, $value);
    }
}