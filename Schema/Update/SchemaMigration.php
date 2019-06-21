<?php


namespace Vaderlab\EAV\Core\Schema\Update;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Entity\Schema;
use Vaderlab\EAV\Core\Schema\Diff\DiffInterface;

/**
 * @TODO: Temporary solution for create and update attribute. Fast implementation.
 *
 * Class SchemaMigration
 * @package Vaderlab\EAV\Core\Schema\Update
 */
class SchemaMigration implements SchemaMigrationInterface
{

    /**
     * @var DiffInterface
     */
    private $diffService;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository|\Vaderlab\EAV\Core\Repository\SchemaRepository
     */
    private $schemaRepository;

    /**
     * SchemaMigration constructor.
     * @param DiffInterface $diff
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        DiffInterface $diff,
        EntityManagerInterface $entityManager
    ) {
        $this->diffService = $diff;
        $this->entityManager = $entityManager;
        $this->schemaRepository = $entityManager->getRepository(Schema::class);
    }

    /**
     * @return string
     */
    public function migrate(): string
    {
        $diff = $this->diffService->diff();

        dump($diff);

        if(count($diff) === 0) {
            return self::SCHEMA_NO_CHANGES;
        }

        foreach ($diff as $schemaClass => $schemaDiff) {
            $this->schemaMigrate($schemaClass, $schemaDiff);
        }

        return self::SCHEMA_MIGRATED;
    }

    /**
     * @param string $classname
     * @param array $schemaDiff
     */
    protected function schemaMigrate(string $classname, array $schemaDiff): void
    {
        $schema = $this->getSchema($classname, $schemaDiff);

        $this->updateSchema($schema, $schemaDiff);
        $this->entityManager->persist($schema);
        $this->entityManager->flush();
    }

    protected function getSchema(string $classname, array $config)
    {
        if($config['status'] === DiffInterface::SCHEMA_CREATE)
        {
            return $this->createNewSchema($config);
        }

        $schema = $this->findSchemaByClassname($classname);

        return $schema;
    }

    protected function updateSchema(Schema $schema, array $config): void
    {
        $schemaStatus = $config['status'];
        if($schemaStatus === DiffInterface::SCHEMA_CREATE) {
            return;
        }

        $attrDiff = isset($config['attributes']) ? $config['attributes'] : [];

        foreach ($attrDiff as $attrName => $item) {
            $status = isset($item['status']) ? $item['status']: DiffInterface::ATTRIBUTE_UPDATE;
            if($status === DiffInterface::ATTRIBUTE_ADD) {
                $attribute = $this->createNewAttributeObject($item['data']);
                $attribute->setSchema($schema);
                $schema->getAttributes()->add($attribute);
            } else if($status === DiffInterface::ATTRIBUTE_UPDATE) {
                $attribute = $this->getAttributeInSchema($schema, $attrName, $status);
                $this->updateAttributeObject($attribute, $item);
            }
        }
    }

    /**
     * @param Schema $schema
     * @param string $attrName
     * @param string $status
     * @return Attribute
     */
    protected function getAttributeInSchema(Schema $schema, string $attrName, string $status): Attribute
    {
        $attributes = $schema->getAttributes();

        return  $attributes->filter(function ($attribute) use ($attrName) {
            /** @var Attribute $attribute */
            return $attribute->getName() === $attrName;
        })->first();
    }

    /**
     * @param array $schemaConfig
     * @return Schema
     */
    protected function createNewSchema(array $schemaConfig): Schema
    {
        $schemaData = $schemaConfig['data'];
        $name = $schemaData['name'];
        $class = $schemaData['class'];
        $attributes = new ArrayCollection();

        foreach ($schemaData['attributes'] as $attributeCfg) {
            $attributes->add($this->createNewAttributeObject($attributeCfg));
        }

        $schema = $this->schemaRepository->createSchema($name, $attributes);
        $schema->setEntityClass($class);

        return $schema;
    }

    /**
     * @param string $classname
     * @return Schema
     */
    protected function findSchemaByClassname(string $classname): Schema
    {
        return $this->schemaRepository->findOneBy([
            'entityClass'   => $classname
        ]);
    }

    /**
     * @todo: temporary solution
     *
     * @param array $attributeConfig
     * @return Attribute
     */
    protected function createNewAttributeObject(array $attributeConfig): Attribute
    {
        $attribute = new Attribute();
        $attribute->setIsUnique($attributeConfig['unique']);
        $attribute->setType($attributeConfig['type']);
        $attribute->setNullable($attributeConfig['nullable']);
        $attribute->setDescription($attributeConfig['description']);
        $attribute->setLength($attributeConfig['length']);
        $attribute->setName($attributeConfig['name']);
        $attribute->setDefaultValue($attributeConfig['defaultValue']);
        $attribute->setIndexable($attributeConfig['indexable']);

        return $attribute;
    }

    /**
     * @todo: temporary solution
     *
     * @param Attribute $attribute
     * @param array $attributeConfig
     */
    protected function updateAttributeObject(Attribute $attribute, array $attributeConfig): void
    {
        foreach ($attributeConfig as $attrName => $item) {
            $val = $attributeConfig[$attrName]['new'];
            switch ($attrName) {
                case 'unique':
                    $attribute->setIsUnique($val);
                    break;
                case 'type':
                    $attribute->setType($val);
                    break;
                case 'nullable':
                    $attribute->setNullable($val);
                    break;
                case 'description':
                    $attribute->setDescription($val);
                    break;
                case 'length':
                    $attribute->setLength($val);
                    break;
                case 'name':
                    $attribute->setName($val);
                    break;
                case 'defaultValue':
                    $attribute->setDefaultValue($val);
                    break;
                case 'indexable':
                    $attribute->setIndexable($val);
                    break;
            }
        }
    }
}