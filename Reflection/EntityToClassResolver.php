<?php


namespace Vaderlab\EAV\Core\Reflection;


use Vaderlab\EAV\Core\Annotation\Attribute;
use Vaderlab\EAV\Core\Annotation\BaseAttribute;
use Vaderlab\EAV\Core\Annotation\Id;
use Vaderlab\EAV\Core\Entity\Entity;
use \ReflectionObject;
use Vaderlab\EAV\Core\Exception\Service\Reflection\ReflectionException;
use Vaderlab\EAV\Core\Service\Entity\EntityServiceInterface;

/**
 * Class EntityObjectResolver
 * @package Vaderlab\EAV\Core\Reflection
 */
class EntityToClassResolver
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
     * @var EntityClassMetaResolver
     */
    private $metaResolverService;

    /**
     * EntityToClassResolver constructor.
     * @param EntityServiceInterface $entityService
     * @param Reflection $reflection
     * @param EntityClassMetaResolver $entityClassMetaResolver
     */
    public function __construct(
        EntityServiceInterface $entityService,
        Reflection $reflection,
        EntityClassMetaResolver $entityClassMetaResolver
    ) {
        $this->entityService = $entityService;
        $this->metaResolverService = $entityClassMetaResolver;
        $this->reflectionService = $reflection;
    }

    /**
     * @param Entity $entity
     * @return object
     * @throws ReflectionException
     * @throws \ReflectionException
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

        $entityId       = $entity->getId();
        $entityObject   = $this->reflectionService->createObject($entityClass);
        $entityRef      = $this->reflectionService->createReflectionObject($entityObject);
        $entityRefClass = $this->reflectionService->createReflectionClass($entityClass);
        /** @var array<Attribute> $protectedAttrs */
        $protectedAttrs = $this->metaResolverService->getProtectedAttributes($entityRefClass);
        $idAttr         = $this->metaResolverService->getIdProperty($entityRefClass);

        /** @var Attribute $attribute */
        foreach ($protectedAttrs as $attribute) {
            $this->updateEntityObjectAttribute(
                $entity,
                $attribute,
                $entityRef,
                $entityObject
            );
        }

        if(!$entityId) {
            return $entityObject;
        }

        $this->updateEntityObjectByAttributeName(
            $idAttr->target,
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
     * @throws ReflectionException
     * @throws \ReflectionException
     */
    protected function updateEntityObjectAttribute(
        Entity $entity,
        BaseAttribute $attribute,
        ReflectionObject $reflectionObject,
        object $entityObject
    ): void
    {

        if(($attribute instanceof Id)) {
            return;
        }

        $target = $attribute->target;
        $attrName = $attribute->name;
        $currentValue = $this->entityService->getValue($entity, $attrName);
        $this->updateEntityObjectByAttributeName(
            $target,
            $reflectionObject,
            $entityObject,
            $currentValue,
            false
        );
    }


    /**
     * @param string $attribute
     * @param ReflectionObject $reflectionObject
     * @param object $entityObject
     * @param null $value
     * @param bool $strict
     * @throws ReflectionException
     * @throws \ReflectionException
     */
    protected function updateEntityObjectByAttributeName(
        string $attribute,
        ReflectionObject $reflectionObject,
        object $entityObject,
        $value = null,
        bool $strict = true
    ): void
    {
        try {
            $this->reflectionService->setReflectionAttributeValue($entityObject, $reflectionObject, $attribute, $value);
        } catch (ReflectionException $e) {
            if($strict) {
                throw $e;
            }
        }
    }
}