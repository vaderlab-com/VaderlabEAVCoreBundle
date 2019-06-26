<?php


namespace Vaderlab\EAV\Core\Subscriber\Attribute;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Exception\Attribute\ProtectedAttributeUpdateDeniedException;
use Vaderlab\EAV\Core\Exception\Attribute\PrptectedAttributeRemoveDeniedException;
use Vaderlab\EAV\Core\Model\AttributeInterface;
use Vaderlab\EAV\Core\Schema\Diff\Comparison\AttributeCompareProcessor;
use Vaderlab\EAV\Core\Schema\Discover\SchemaDiscoverInterface;

class AttributeUpdateSubscriber implements EventSubscriber
{
    /**
     * @var AttributeCompareProcessor
     */
    private $attributeCompareProcessor;

    /**
     * @var SchemaDiscoverInterface
     */
    private $fsDiscover;

    /**
     * AttributeUpdateSubscriber constructor.
     * @param SchemaDiscoverInterface $fsDiscover
     * @param AttributeCompareProcessor $attributeCompareProcessor
     */
    public function __construct(
        SchemaDiscoverInterface $fsDiscover,
        AttributeCompareProcessor $attributeCompareProcessor
    ) {
        $this->fsDiscover = $fsDiscover;
        $this->attributeCompareProcessor = $attributeCompareProcessor;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
            Events::preRemove,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $data           = $this->process($args);
        if($data === null) {
            return;
        }

        $ormAttr        = $data['orm_attribute'];
        $fsAttribute    = $data['fs_attribute'];
        $ormSchema      = $data['orm_schema'];

        $diff = $this->attributeCompareProcessor->process($ormAttr, $fsAttribute);

        if(!count($diff)) {
            return;
        }

        throw new ProtectedAttributeUpdateDeniedException($fsAttribute, $ormSchema);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        $ormAttr        = $args->getObject();
        if(!($ormAttr instanceof Attribute)) {
            return;
        }

        $args->getObjectManager()->refresh($ormAttr);

        $data = $this->process($args);
        if($data === null) {
            return;
        }

        throw new PrptectedAttributeRemoveDeniedException(
            $data['orm_attribute'],
            $data['orm_schema']);
    }

    /**
     * @param string $entityClass
     * @param string $attrName
     * @return AttributeInterface|null
     */
    protected function getFsAttribute(string $entityClass, string $attrName): ?AttributeInterface
    {
        $fsSchema = $this->fsDiscover->getSchemaByClass($entityClass);

        $fsAttribute = $fsSchema->getAttributes()->filter(function (AttributeInterface $attribute) use ($attrName) {
            return $attribute->getName() === $attrName;
        })->first();

        return $fsAttribute ?: null;
    }

    /**
     * @param LifecycleEventArgs $args
     * @return array|null
     */
    protected function process(LifecycleEventArgs $args): ?array
    {
        $ormAttr        = $args->getObject();
        if(!($ormAttr instanceof Attribute)) {
            return null;
        }

        $attributeName = $ormAttr->getName();
        if(($args instanceof PreUpdateEventArgs)) {
            $updatedValues = $args->getEntityChangeSet();
            if(isset($updatedValues['name'])) {
                $attributeName = $updatedValues['name'][0];
            }
        }

        $ormSchema      = $ormAttr->getSchema();
        $entityClass    = $ormSchema->getEntityClass();
        if(!$entityClass) {
            return null;
        }

        $fsAttribute = $this->getFsAttribute($entityClass, $attributeName);

        if(!$fsAttribute) {
            return null;
        }

        return [
            'orm_schema'    => $ormSchema,
            'fs_attribute'  => $fsAttribute,
            'orm_attribute' => $ormAttr
        ];
    }
}