<?php


namespace Vaderlab\EAV\Core\Service\ORM;


use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Vaderlab\EAV\Core\Entity\Entity;
use Vaderlab\EAV\Core\Reflection\ClassToEntityResolver;
use Vaderlab\EAV\Core\Reflection\EntityToClassResolver;

class EntityManager implements EAVEntityManagerInterface
{
    /**
     * @var ClassToEntityResolver
     */
    private $classToEntityResolver;

    /**
     * @var EntityToClassResolver
     */
    private $entityToClassResolver;

    /**
     * @var
     */
    private $entityManager;

    /**
     * EntityManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param ClassToEntityResolver $classToEntityResolver
     * @param EntityToClassResolver $entityToClassResolver
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ClassToEntityResolver $classToEntityResolver,
        EntityToClassResolver $entityToClassResolver
    ) {
        $this->entityManager            = $entityManager;
        $this->entityToClassResolver    = $entityToClassResolver;
        $this->classToEntityResolver    = $classToEntityResolver;
    }

    /**
     * @param $entity
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     */
    public function persist($entity): void
    {
        $this->entityManager->persist($this->resolveEAVEntity($entity));
    }

    /**
     * @param $entity
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     */
    public function detach($entity): void
    {
        $this->entityManager->persist($this->resolveEAVEntity($entity));
    }

    /**
     *
     */
    public function flush(): void
    {
        $this->entityManager->flush();
    }

    /**
     * @param object $object
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     */
    public function remove(object $object): void
    {
        $this->entityManager->remove($this->resolveEAVEntity($object));
    }

    /**
     * @param object $object
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     */
    public function refresh(object $object): void
    {
        $this->entityManager->refresh($this->resolveEAVEntity($object));
    }

    /**
     * @param object $entity
     * @return Entity
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     */
    protected function resolveEAVEntity(object $entity): Entity
    {
        if($entity instanceof Entity) {
            return $entity;
        }

        return $this->classToEntityResolver->resolve($entity);
    }
}