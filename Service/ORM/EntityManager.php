<?php


namespace Vaderlab\EAV\Core\Service\ORM;


use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Decorator\EntityManagerDecorator;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\Query\Expr\Join;
use Vaderlab\EAV\Core\Entity\Entity;
use Vaderlab\EAV\Core\Reflection\ClassToEntityResolver;
use Vaderlab\EAV\Core\Reflection\EntityToClassResolver;
use Vaderlab\EAV\Core\Repository\EntityRepository;

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
     * EntityManager constructor.
     * @param ObjectManager $entityManager
     * @param ClassToEntityResolver $classToEntityResolver
     * @param EntityToClassResolver $entityToClassResolver
     */
    public function __construct(
        ObjectManager $entityManager,
        ClassToEntityResolver $classToEntityResolver,
        EntityToClassResolver $entityToClassResolver
    ) {
        $this->entityManager            = $entityManager;
        $this->entityToClassResolver    = $entityToClassResolver;
        $this->classToEntityResolver    = $classToEntityResolver;
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
        if(!$this->isEavEntityClass($entityName)) {
            return $this->entityManager->find($entityName, $id);
        }

        $repository = $this->getEntityRepository();
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
        if(!$this->isEAVEntity($entity)) {
            $this->entityManager->persist($entity);

            return;
        }

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
        if(!$this->isEAVEntity($entity)) {
            $this->entityManager->detach($entity);

            return;
        }

        $this->entityManager->detach($this->resolveEAVEntity($entity));
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
        if(!$this->isEAVEntity($object)) {
            $this->entityManager->remove($object);

            return;
        }

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
    public function refresh($object): void
    {
        if(!$this->isEAVEntity($object)) {
            $this->entityManager->refresh($object);

            return;
        }

        $this->entityManager->refresh($this->resolveEAVEntity($object));
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
        if(!$this->isEAVEntity($object)) {
            $this->entityManager->contains($object);

            return;
        }

        return $this->entityManager->contains($this->resolveEAVEntity($object));
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
        if(!$this->isEAVEntity($object)) {
            $this->entityManager->initializeObject($object);

            return;
        }

        $this->entityManager->initializeObject($this->resolveEAVEntity($object));
    }

    /**
     * @param object $entity
     * @return Entity|null
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     */
    protected function resolveEAVEntity($entity = null): ?Entity
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
    protected function isEavEntityClass(string $classname): bool
    {
        $repository = $this->getEntityRepository();
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
    protected function isEAVEntity(object $object): bool
    {
        return $this->isEavEntityClass(get_class($object));
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository|\Vaderlab\EAV\Core\Repository\EntityRepository
     */
    protected function getEntityRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Entity::class);
    }
}