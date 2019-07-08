<?php


namespace Vaderlab\EAV\Core\Event\Schema;


use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Vaderlab\EAV\Core\Entity\Schema;
use Vaderlab\EAV\Core\Exception\Schema\ProtectedSchemaRemoveException;
use Vaderlab\EAV\Core\Exception\Schema\ProtectedSchemaUpdateException;
use Vaderlab\EAV\Core\Model\SchemaInterface;
use Vaderlab\EAV\Core\Schema\Diff\Comparison\AttributeCompareProcessor;
use Vaderlab\EAV\Core\Schema\Diff\Comparison\SchemaCompareProcessor;
use Vaderlab\EAV\Core\Schema\Discover\SchemaDiscoverInterface;

class SchemaUpdateSubscriber implements EventSubscriber
{
    /**
     * @var SchemaDiscoverInterface
     */
    private $fsDiscover;

    /**
     * @var SchemaCompareProcessor
     */
    private $schemaCompareProcessor;

    /**
     * SchemaUpdateSubscriber constructor.
     * @param SchemaDiscoverInterface $fsDiscover
     * @param SchemaCompareProcessor $schemaCompareProcessor
     */
    public function __construct(
        SchemaDiscoverInterface $fsDiscover,
        SchemaCompareProcessor $schemaCompareProcessor
    ) {
        $this->fsDiscover = $fsDiscover;
        $this->schemaCompareProcessor = $schemaCompareProcessor;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
            'preRemove',
            'preUpdate',
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $data = $this->getSchemesForComparing($args);
        $cData = count($data);
        if($cData === 0) {
            return;
        }

        $sourceSchema = $data[0];
        $fsSchema = isset($data[1]) ? $data[1] : null;
        if(!$fsSchema) {
            return;
        }

        $diff = $this->schemaCompareProcessor->process($sourceSchema, $fsSchema);
        if(!count($diff)) {
            return;
        }

        throw new ProtectedSchemaUpdateException($sourceSchema);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        $data = $this->getSchemesForComparing($args);
        if(count($data) !== 2 || !isset($data[1])) {
            return;
        }

        throw new ProtectedSchemaRemoveException($data[0]);
    }

    /**
     * @param LifecycleEventArgs $args
     * @return array<SchemaInterface>
     */
    protected function getSchemesForComparing(LifecycleEventArgs $args): array
    {
        $sourceSchema = $args->getObject();
        if(!($sourceSchema instanceof SchemaInterface)) {
            return [];
        }

        $sourceClass = $sourceSchema->getEntityClass();
        if(!$sourceClass) {
            return [$sourceSchema, null];
        }

        $fsSchema = $this->fsDiscover->getSchemaByClass($sourceClass);
        if(!$fsSchema) {
            return [$sourceSchema, null];
        }

        return [
            $sourceSchema,
            $fsSchema,
        ];
    }
}
