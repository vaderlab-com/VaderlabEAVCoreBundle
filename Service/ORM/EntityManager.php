<?php


namespace Vaderlab\EAV\Core\Service\ORM;


use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\Decorator\EntityManagerDecorator;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\Query\Expr\Join;
use Vaderlab\EAV\Core\Entity\Entity;
use Vaderlab\EAV\Core\Reflection\ClassToEntityResolver;
use Vaderlab\EAV\Core\Reflection\EntityToClassResolver;
use Vaderlab\EAV\Core\Repository\EntityRepository;
use Vaderlab\EAV\Core\Service\Entity\EntityServiceInterface;
use Vaderlab\EAV\Core\Service\Entity\EntityServiceORM;
use Vaderlab\EAV\Core\Service\Entity\EntityServiceProxy;

/**
 * @method Mapping\ClassMetadata getClassMetadata($className)
 */
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
     * @var EntityServiceProxy
     */
    private $entityServiceProxy;

    /**
     * EntityManager constructor.
     * @param ObjectManager $entityManager
     * @param ClassToEntityResolver $classToEntityResolver
     * @param EntityToClassResolver $entityToClassResolver
     */
    public function __construct(
        ObjectManager $entityManager,
        ClassToEntityResolver $classToEntityResolver,
        EntityToClassResolver $entityToClassResolver,
        EntityServiceProxy $entityServiceProxy
    ) {
        $this->entityManager            = $entityManager;
        $this->entityToClassResolver    = $entityToClassResolver;
        $this->classToEntityResolver    = $classToEntityResolver;
        $this->entityServiceProxy       = $entityServiceProxy;
    }

    /**
     * @param string $classname
     * @return ObjectRepository
     */
    public function getRepository(string $classname): ObjectRepository
    {
        return $this->entityManager->getRepository($classname);
    }

    /**
     * @param $entityName
     * @param $id
     * @return object|null
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ReflectionException
     */
    public function find($entityName, $id)
    {
        if(!$this->getEntityService()->isEavEntityClass($entityName)) {
            return $this->entityManager->find($entityName, $id);
        }

        $repository = $this->getEntityService()->getEAVEntityRepository();
        /** @var Entity $result */
        $result = $repository->find($id);

        if(!$result) {
            return null;
        }

        return $this->entityToClassResolver->resolve($result);
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
        if(!$this->getEntityService()->isEAVEntity($entity)) {
            $this->entityManager->persist($entity);

            return;
        }

        $this->entityManager->persist($this->getEntityService()->resolveEAVEntity($entity));
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
        if(!$this->getEntityService()->isEAVEntity($entity)) {
            $this->entityManager->detach($entity);

            return;
        }

        $this->entityManager->detach($this->getEntityService()->resolveEAVEntity($entity));
    }

    /**
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
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
    public function remove($object): void
    {
        if(!$this->getEntityService()->isEAVEntity($object)) {
            $this->entityManager->remove($object);

            return;
        }

        $this->entityManager->remove($this->getEntityService()->resolveEAVEntity($object));
    }

    /**
     * @param object $object
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     */
    public function refresh($object): void
    {
        if(!$this->getEntityService()->isEAVEntity($object)) {
            $this->entityManager->refresh($object);

            return;
        }

        $this->entityManager->refresh($this->getEntityService()->resolveEAVEntity($object));
    }

    /**
     * @param $object
     * @return bool|void
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     */
    public function contains($object)
    {
        if(!$this->getEntityService()->isEAVEntity($object)) {
            $this->entityManager->contains($object);

            return;
        }

        return $this->entityManager->contains($this->getEntityService()->resolveEAVEntity($object));
    }

    /**
     * @param $object
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     */
    public function initializeObject($object)
    {
        if(!$this->getEntityService()->isEAVEntity($object)) {
            $this->entityManager->initializeObject($object);

            return;
        }

        $this->entityManager->initializeObject($this->resolveEAVEntity($object));
    }

    /**
     * @return EntityServiceORM
     */
    protected function getEntityService(): EntityServiceORM
    {
        return $this->entityServiceProxy->getService();
    }
}