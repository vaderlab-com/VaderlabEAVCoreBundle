<?php


namespace Vaderlab\EAV\Core\Schema\Discover\File;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vaderlab\EAV\Core\Annotation\Id;
use Vaderlab\EAV\Core\Entity\Schema;
use Vaderlab\EAV\Core\Model\SchemaInterface;
use Vaderlab\EAV\Core\Reflection\EntityClassMetaResolver;
use Vaderlab\EAV\Core\Reflection\Reflection;
use Vaderlab\EAV\Core\Schema\Discover\SchemaDiscoverInterface;

class SchemaDiscover implements SchemaDiscoverInterface
{
    /**
     * @var ProtectedSchemasDiscovery
     */
    private $schemasDiscovery;

    /**
     * @var Reflection
     */
    private $reflection;
    /**
     * @var EntityClassMetaResolver
     */
    private $classMetaResolver;

    /**
     * FileSchema constructor.
     * @param ProtectedSchemasDiscovery $schemasDiscovery
     * @param Reflection $reflection
     * @param EntityClassMetaResolver $classMetaResolver
     */
    public function __construct(
        ProtectedSchemasDiscovery $schemasDiscovery,
        Reflection $reflection,
        EntityClassMetaResolver $classMetaResolver
    ) {
        $this->schemasDiscovery = $schemasDiscovery;
        $this->reflection = $reflection;
        $this->classMetaResolver = $classMetaResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function getSchemes(): Collection
    {
        return $this->getSchemasClasses();
    }

    /**
     * @param string $classname
     * @return Schema
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException
     */
    public function getSchemaByClass(string $classname): SchemaInterface
    {
        return $this->generateEntitySchema($classname);
    }

    /**
     * @return array
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException
     */
    protected function getSchemasClasses(): ArrayCollection
    {
        $classes = $this->schemasDiscovery->discover();

        $app = $classes['app'];
        $vendors = $classes['vendor'];

        $schema = new ArrayCollection();
        foreach ($app as $entityClass) {
            $schema->add($this->generateEntitySchema($entityClass));
        }

        foreach ($vendors as $vendor) {
            foreach ($vendor as $entityClass) {
                $schema->add($this->generateEntitySchema($entityClass));
            }
        }

        return $schema;
    }

    /**
     * @param string $class
     * @return Schema
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException
     */
    protected function generateEntitySchema(string $class): SchemaInterface
    {
        $refClass               = $this->reflection->createReflectionClass($class);
        $attributeSchema        = new ArrayCollection($this->classMetaResolver->getProtectedAttributes($refClass));
        $protectedAnnotation    = $this->classMetaResolver->getProtectedEntityAnnotation($class);
        $schemaName             = $protectedAnnotation->name ?: $refClass->getShortName();


        $attributes = $attributeSchema->filter(function ($attribute) {
            return !($attribute instanceof Id);
        });

        return new FileSchema($schemaName, $class, $attributes);
    }
}