<?php


namespace Vaderlab\EAV\Core\Schema\Discover\Database;


use Doctrine\ORM\EntityManagerInterface;
use Vaderlab\EAV\Core\Entity\Schema;
use Vaderlab\EAV\Core\Schema\Discover\SchemaDiscoverInterface;

class SchemaDiscover implements SchemaDiscoverInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getSchema(): array
    {
        $schemaRepository = $this->entityManager->getRepository(Schema::class);
        $allSchemas = $schemaRepository->findAll();
        $result = [];
        /** @var Schema $schema */
        foreach ($allSchemas as $schema) {
            $schema->getAttributes();
        }

        return [];
    }

    protected function buildSchemaArray(Schema $schema): array
    {
        $classname = $schema->getEntityClass();
        $attributes = $schema->getAttributes();
        $name = $schema->getName();

        return [
            $classname  => [
                ''
            ],
        ];
    }
}