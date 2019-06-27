<?php


namespace Vaderlab\EAV\Core\Service\Schema;


use ArrayAccess;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Entity\Schema;
use Vaderlab\EAV\Core\Model\SchemaInterface;


class EAVSchemaManager implements EAVSchemaManagerInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository|\Vaderlab\EAV\Core\Repository\SchemaRepository
     */
    private $schemaRepository;

    /**
     * SchemaManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->schemaRepository = $entityManager->getRepository(Schema::class);
    }

    /**
     * @param String $name
     * @param ArrayAccess $attributes
     * @return Schema
     */
    public function createSchema(String $name, ArrayAccess $attributes): SchemaInterface
    {
        $schema = new Schema();
        $schema->setName($name);
        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            $attribute->setSchema($schema);
        }

        $schema->setAttributes($attributes);

        return $schema;
    }

    /**
     * @param string $classname
     * @return Schema|null
     */
    public function findByClass(string $classname): ?SchemaInterface
    {
        return $this->schemaRepository->findOneBy([
            'entityClass'   => $classname
        ]);
    }

    /**
     * @param string $name
     * @return Schema|null
     */
    public function findByName(string $name): ?SchemaInterface
    {
        $this->schemaRepository->findOneBy([
            'name'   => $name
        ]);
    }

    /**
     * @return ArrayCollection<SchemaInterface>
     */
    public function findAll(): ArrayCollection
    {
        return new ArrayCollection($this->schemaRepository->findAll());
    }

    /**
     * @return ArrayCollection<SchemaInterface>
     */
    public function findAllProtectedSchemes(): ArrayCollection
    {
        $schemes = new ArrayCollection($this->schemaRepository->findAll());

        return $schemes->filter(function (Schema $schema) {
            return !!$schema->getEntityClass();
        });
    }

    /**
     * @param int $id
     * @return SchemaInterface|null
     */
    public function findById(int $id): ?SchemaInterface
    {
        return $this->schemaRepository->findOneBy(['id' => $id]);
    }
}