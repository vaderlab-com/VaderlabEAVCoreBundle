<?php


namespace Vaderlab\EAV\Core\Schema\Discover\File;


use Vaderlab\EAV\Core\Annotation\Attribute;
use Vaderlab\EAV\Core\Annotation\Id;
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
    public function getSchema(): array
    {
        return $this->getSchemasClasses();
    }

    /**
     * @return array
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException
     */
    protected function getSchemasClasses()
    {
        $classes = $this->schemasDiscovery->discover();

        $app = $classes['app'];
        $vendors = $classes['vendor'];

        $schema = [];
        foreach ($app as $entityClass) {
            $this->pushToSchemaArray($entityClass, $schema);
        }

        foreach ($vendors as $vendor) {
            foreach ($vendor as $class) {
                $this->pushToSchemaArray($class, $schema);
            }
        }

        return $schema;
    }

    /**
     * @param string $class
     * @param array $output
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException
     */
    protected function pushToSchemaArray(string $class, array &$output)
    {
        $output[$class] = $this->generateEntitySchema($class);
    }

    /**
     * @param string $class
     * @return array
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException
     */
    protected function generateEntitySchema(string $class): array
    {
        $refClass = $this->reflection->createReflectionClass($class);
        $attributes = $this->classMetaResolver->getProtectedAttributes($refClass);

        $attributeSchema = [];

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            if($attribute instanceof Id) {
                continue;
            }

            $attributeSchema[] = [
                'name'  => $attribute->name,
                'defaultValue'  => $attribute->default,
                'type'  => $attribute->type,
                'length'    => $attribute->length,
                'nullable'  => $attribute->nullable,
                'indexable' => $attribute->indexable,
                'unique'    => $attribute->unique,
                'description'   => $attribute->description,
            ];
        }

        return $attributeSchema;
    }
}