<?php


namespace Vaderlab\EAV\Core\Schema\Diff;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use function foo\func;
use Vaderlab\EAV\Core\Entity\Schema;
use Vaderlab\EAV\Core\Model\SchemaInterface;
use Vaderlab\EAV\Core\Repository\SchemaRepository;
use Vaderlab\EAV\Core\Schema\Discover\SchemaDiscoverInterface;

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
     * Diff constructor.
     * @param SchemaDiscoverInterface $dbDiscover
     * @param SchemaDiscoverInterface $fsDiscover
     */
    public function __construct(
        SchemaDiscoverInterface $dbDiscover,
        SchemaDiscoverInterface $fsDiscover,
        EntityManagerInterface $entityManager
    )
    {
        $this->dbDiscover = $dbDiscover;
        $this->fsDiscover = $fsDiscover;
        $this->entityManager = $entityManager;
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
                continue;
                $dbSchema = $this->createNewSchema($fsSchema->getName());
            }

        }

    }

    protected function diffSchema()
    {

    }

    /**
     * @param string $name
     * @return Schema
     */
    protected function createNewSchema(string $name): Schema
    {
        $this->getSchemaRepository()->createSchema($name, []);
    }

    /**
     * @return SchemaRepository
     */
    protected function getSchemaRepository(): SchemaRepository
    {
        return $this->entityManager->getRepository(Schema::class);
    }
}