<?php


namespace Vaderlab\EAV\Core\Event\Attribute;


use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Entity\UniqueIndex;
use Vaderlab\EAV\Core\Schema\Diff\Comparison\AttributeCompareProcessor;
use Vaderlab\EAV\Core\ORM\Value\UniqueIndexManager;

class CreateUniqueAttributeSubscriber implements EventSubscriber
{
    /**
     * @var AttributeCompareProcessor
     */
    private $attributeCompareProcessor;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var $uniqueIndexManager
     */
    private $uniqueIndexManager;

    /**
     * CreateUniqueAttributeSubscriber constructor.
     * @param AttributeCompareProcessor $attributeCompareProcessor
     * @param EntityManagerInterface $entityManager
     * @param UniqueIndexManager $uniqueIndexManager
     */
    public function __construct(
        AttributeCompareProcessor $attributeCompareProcessor,
        EntityManagerInterface $entityManager,
        UniqueIndexManager $uniqueIndexManager
    ) {
        $this->entityManager = $entityManager;
        $this->attributeCompareProcessor = $attributeCompareProcessor;
        $this->uniqueIndexManager = $uniqueIndexManager;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
//            Events::prePersist, //TODO:
            Events::preUpdate,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $this->index($args);
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    private function index(PreUpdateEventArgs $args): void
    {
        $attribute = $args->getObject();
        if(!($attribute instanceof Attribute)) {
            return;
        }

        $changeSet = $args->getEntityChangeSet();
        if(!isset($changeSet['isUnique'])) {
            return;
        }

        $uniqueData = $changeSet['isUnique'];

        if($uniqueData[0] === true) {
            $this->switchOff($attribute);

            return;
        }

        $this->switchOn($attribute, $this->entityManager);
    }

    /**
     * @param Attribute $attribute
     * @param EntityManagerInterface $em
     */
    private function switchOn(Attribute $attribute, ObjectManager $em ): void
    {
        $idx = $this->uniqueIndexManager->generateIndexForAttribute($attribute);
        /** @var UniqueIndex $tmp */
        foreach ($idx as $tmp) {
            $this->entityManager->persist($tmp);
        }

        $this->entityManager->flush();
    }

    /**
     * @param Attribute $attribute
     */
    private function switchOff(Attribute $attribute): void
    {
        $this->uniqueIndexManager->removeIndexFromAttribute($attribute);
    }
}