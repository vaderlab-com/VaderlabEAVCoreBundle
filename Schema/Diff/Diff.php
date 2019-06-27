<?php


namespace Vaderlab\EAV\Core\Schema\Diff;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use function foo\func;
use Vaderlab\EAV\Core\Entity\Schema;
use Vaderlab\EAV\Core\Model\SchemaInterface;
use Vaderlab\EAV\Core\Repository\SchemaRepository;
use Vaderlab\EAV\Core\Schema\Diff\Comparison\SchemaCompareProcessor;
use Vaderlab\EAV\Core\Schema\Discover\SchemaDiscoverInterface;
use Vaderlab\EAV\Core\Service\Schema\EAVSchemaManager;
use Vaderlab\EAV\Core\Service\Schema\EAVSchemaManagerInterface;

/**
 * Class Diff
 * @package Vaderlab\EAV\Core\Schema\Diff
 *
 * @todo: temporary fast solution !!!!!
 */
class Diff implements DiffInterface
{
    /**
     * @var SchemaDiscoverInterface
     */
    private $dbDiscover;

    /**
     * @var SchemaDiscoverInterface
     */
    private $fsDiscover;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SchemaCompareProcessor
     */
    private $schemaCompareProcessor;

    /**
     * @var EAVSchemaManager
     */
    private $schemaManager;

    /**
     * Diff constructor.
     * @param SchemaDiscoverInterface $dbDiscover
     * @param SchemaDiscoverInterface $fsDiscover
     * @param SchemaCompareProcessor $schemaCompareProcessor
     * @param EntityManagerInterface $entityManager
     * @param EAVSchemaManagerInterface $schemaManager
     */
    public function __construct(
        SchemaDiscoverInterface $dbDiscover,
        SchemaDiscoverInterface $fsDiscover,
        SchemaCompareProcessor $schemaCompareProcessor,
        EntityManagerInterface $entityManager,
        EAVSchemaManagerInterface $schemaManager
    )
    {
        $this->dbDiscover = $dbDiscover;
        $this->fsDiscover = $fsDiscover;
        $this->entityManager = $entityManager;
        $this->schemaCompareProcessor = $schemaCompareProcessor;
        $this->schemaManager = $schemaManager;
    }

    /**
     * Create difference between file and database schemas
     * @return array
     */
    public function diff(bool $apply = false): array
    {
        $diff                = [];
        $fsSchemas           = $this->fsDiscover->getSchemes();
        $dbSchemas           = $this->dbDiscover->getSchemes();
        $currentSchemaClass  = null;
        $filter              = function(SchemaInterface $schema) use (&$currentSchemaClass){
            return $currentSchemaClass === $schema->getEntityClass();
        };

        /** @var SchemaInterface $fsSchema */
        foreach ($fsSchemas as $fsSchema) {
            $currentSchemaClass  = $fsSchema->getEntityClass();
            $dbSchema            = $dbSchemas->filter($filter)->first();
            if(!$dbSchema) {
                $dbSchema = $this->createNewSchema($fsSchema->getName());
            }

            $tmpDiff = $this->schemaCompareProcessor->process($dbSchema, $fsSchema, $apply);
            if(!count($tmpDiff)) {
                continue;
            }

            if($apply) {
                $this->entityManager->persist($dbSchema);
                $this->entityManager->flush();
            }

            $diff[$currentSchemaClass] = $tmpDiff;
        }

        return $diff;
    }

    /**
     * @param string $name
     * @return Schema
     */
    protected function createNewSchema(string $name): Schema
    {
        $this->schemaManager->createSchema($name, new ArrayCollection([]));
    }
}