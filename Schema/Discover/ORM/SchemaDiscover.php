<?php


namespace Vaderlab\EAV\Core\Schema\Discover\ORM;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Vaderlab\EAV\Core\Entity\Schema;
use Vaderlab\EAV\Core\Model\SchemaInterface;
use Vaderlab\EAV\Core\Schema\Discover\SchemaDiscoverInterface;

class SchemaDiscover implements SchemaDiscoverInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * SchemaDiscover constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getSchemes(): Collection
    {
        $schemaRepository = $this->entityManager->getRepository(Schema::class);
        $schemes = new ArrayCollection($schemaRepository->findAll());

        return $schemes->filter(function (SchemaInterface $schema) {
            return !!$schema->getEntityClass();
        });
    }

    /**
     * @param string $classname
     * @return SchemaInterface
     */
    public function getSchemaByClass(string $classname): SchemaInterface
    {
        return $this->entityManager->getRepository(Schema::class)->findOneBy([
            'entityClass' => $classname,
        ]);
    }
}