<?php


namespace Vaderlab\EAV\Core\ORM;


use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\Mapping;
use Vaderlab\EAV\Core\Entity\Entity;
use Vaderlab\EAV\Core\Reflection\ClassToEntityResolver;
use Vaderlab\EAV\Core\Reflection\EntityToClassResolver;
use Vaderlab\EAV\Core\Service\Entity\EAVEntityManagerORM;
use Vaderlab\EAV\Core\Service\Entity\EntityServiceProxy;

/**
 * @method Mapping\ClassMetadata getClassMetadata($className)
 */
class EntityManager implements EntityManagerInterface
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
     * @var PersistentEntityCollection
     */
    private $persistentCollection;

    /**
     * @var PersistentEntityCollection
     */
    private $persistentEntityCollection;

    /**
     * EntityManager constructor.
     * @param ObjectManager $entityManager
     * @param ClassToEntityResolver $classToEntityResolver
     * @param EntityToClassResolver $entityToClassResolver
     * @param EntityServiceProxy $entityServiceProxy
     */
    public function __construct(
        ObjectManager $entityManager,
        ClassToEntityResolver $classToEntityResolver,
        EntityToClassResolver $entityToClassResolver,
        EntityServiceProxy $entityServiceProxy,
        PersistentEntityCollection $persistentEntityCollection
    ) {
        $this->entityManager            = $entityManager;
        $this->entityToClassResolver    = $entityToClassResolver;

        $this->classToEntityResolver    = $classToEntityResolver;
        $this->entityServiceProxy       = $entityServiceProxy;
        $this->persistentCollection     = $persistentEntityCollection;
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
        $es = $this->getEntityService();
        if(!$es->isEavEntityClass($entityName)) {
            return $this->entityManager->find($entityName, $id);
        }

        /** @var Entity $result */
        $result = $es->findByClassAndId($entityName, $id);

        if(!$result) {
            return null;
        }

        return $this->entityToClassResolver->resolve($result);
    }

    /**
     * @param object $entity
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ForeignPropertyException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertyNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException
     */
    public function persist($entity): void
    {
        $this->callAction('persist', $entity);
    }

    /**
     * @param object $entity
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ForeignPropertyException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertyNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException
     */
    public function detach($entity): void
    {
        $this->callAction('detach', $entity);
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
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ForeignPropertyException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertyNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException
     */
    public function remove($object): void
    {
        $this->callAction('remove', $object);
    }

    /**
     * @param object $object
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ForeignPropertyException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertyNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException
     */
    public function refresh($object): void
    {
        $this->callAction('refresh', $object);
    }

    /**
     * @param $object
     * @return mixed
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ForeignPropertyException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertyNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException
     */
    public function contains($object)
    {
        return $this->callAction('contains', $object);
    }

    /**
     * @param $object
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ForeignPropertyException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertyNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException
     */
    public function initializeObject($object)
    {
        $this->callAction('initializeObject', $object);
    }

    /**
     * @return EAVEntityManagerORM
     */
    protected function getEntityService(): EAVEntityManagerORM
    {
        return $this->entityServiceProxy->getService();
    }

    /**
     * @param string $action
     * @param object $object
     * @return mixed
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Entity\UnregisteredEntityAttributeException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ClassToEntityBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ForeignPropertyException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertyNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException
     */
    protected function callAction(string $action, object $object)
    {
        $es = $this->getEntityService();
        $entity = $object;
        $isEavEntity = $es->isEAVEntity($object);
        if($isEavEntity) {
            $entity = $es->resolveEAVEntity($object);
        }

        $result = call_user_func_array([$this->entityManager, $action], [$entity]);

        if($isEavEntity && in_array($action, ['persist'])) {
            $this->persistentCollection->add($entity, $object);
        }

        return $result;
    }
}