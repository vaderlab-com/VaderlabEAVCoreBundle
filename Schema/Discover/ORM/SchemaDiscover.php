<?php


namespace Vaderlab\EAV\Core\Schema\Discover\ORM;


use Doctrine\ORM\EntityManagerInterface;
use Vaderlab\EAV\Core\Entity\Schema;
use Vaderlab\EAV\Core\Schema\Discover\SchemaDiscoverInterface;

class SchemaDiscover implements SchemaDiscoverInterface
{
    private $entityManager;

    private $converter;

    public function __construct(
        EntityManagerInterface $entityManager,
        SchemaToArrayConverter $converter
    ) {
        $this->entityManager = $entityManager;
        $this->converter = $converter;
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
            $this->converter->loadSchema($schema);
            $result[] = $this->converter->convert();
        }

        return $result;
    }
}