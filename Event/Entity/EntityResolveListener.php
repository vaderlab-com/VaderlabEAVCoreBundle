<?php


namespace Vaderlab\EAV\Core\Event\Entity;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Vaderlab\EAV\Core\Entity\Entity;
use Vaderlab\EAV\Core\ORM\PersistentEntityCollection;
use Vaderlab\EAV\Core\Reflection\EntityToClassResolver;

class EntityResolveListener
{

    /**
     * @var PersistentEntityCollection
     */
    private $persistentEntityCollection;

    /**
     * @var EntityToClassResolver
     */
    private $entityToClassResolver;

    /**
     * EntityResolveListener constructor.
     * @param PersistentEntityCollection $persistentEntityCollection
     * @param EntityToClassResolver $entityToClassResolver
     */
    public function __construct(
        PersistentEntityCollection $persistentEntityCollection,
        EntityToClassResolver $entityToClassResolver
    ) {
        $this->persistentEntityCollection = $persistentEntityCollection;
        $this->entityToClassResolver = $entityToClassResolver;
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ForeignPropertyException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ReflectionException
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ForeignPropertyException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ReflectionException
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    /**
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args): void
    {
        $this->persistentEntityCollection->clear();
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \ReflectionException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ForeignPropertyException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\ReflectionException
     */
    protected function process(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Entity) {
            return;
        }

        $eav = $this->persistentEntityCollection->getEavByEntity($entity);
        if($eav === null) {
            return;
        }

        $this->entityToClassResolver->resolve($entity, $eav);
    }
}