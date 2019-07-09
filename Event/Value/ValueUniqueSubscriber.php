<?php


namespace Vaderlab\EAV\Core\Event\Value;


use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Vaderlab\EAV\Core\Entity\AbstractValue;
use Vaderlab\EAV\Core\ORM\Value\UniqueIndexGenerator;

class ValueUniqueSubscriber implements EventSubscriber
{
    /**
     * @var UniqueIndexGenerator
     */
    private $uniqueIndexGenerator;

    /**
     * ValueUniqueSubscriber constructor.
     * @param UniqueIndexGenerator $uniqueIndexGenerator
     */
    public function __construct(UniqueIndexGenerator $uniqueIndexGenerator)
    {
        $this->uniqueIndexGenerator = $uniqueIndexGenerator;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
            'preUpdate',
            'prePersist',
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->run($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->run($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    protected function run(LifecycleEventArgs $args): void
    {
        $value = $args->getObject();
        if(!($value instanceof AbstractValue)) {
            return;
        }

        $attribute = $value->getAttribute();
        $isUnique = $attribute->isUnique();
        if(!($isUnique)) {
            return;
        }

        $idx = $this->uniqueIndexGenerator->generate($value);
        $em = $args->getObjectManager();
        $em->persist($idx);
    }
}