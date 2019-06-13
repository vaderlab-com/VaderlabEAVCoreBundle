<?php


namespace Vaderlab\EAV\Core\Schema\Discover\File;


use Vaderlab\EAV\Core\Annotation\Id;
use Vaderlab\EAV\Core\Schema\Discover\SchemaToArrayConverter;
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
     * @var SchemaToArrayConverter
     */
    private $schemaToArrayConverter;

    /**
     * FileSchema constructor.
     * @param ProtectedSchemasDiscovery $schemasDiscovery
     * @param Reflection $reflection
     * @param EntityClassMetaResolver $classMetaResolver
     */
    public function __construct(
        ProtectedSchemasDiscovery $schemasDiscovery,
        Reflection $reflection,
        EntityClassMetaResolver $classMetaResolver,
        SchemaToArrayConverter $schemaToArrayConverter
    ) {
        $this->schemasDiscovery = $schemasDiscovery;
        $this->reflection = $reflection;
        $this->classMetaResolver = $classMetaResolver;
        $this->schemaToArrayConverter = $schemaToArrayConverter;
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
        $output[] = $this->generateEntitySchema($class);
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
        $attributeSchema = $this->classMetaResolver->getProtectedAttributes($refClass);
        $this->classMetaResolver->getProtectedAttributes($refClass);

        $protectedAnnotation = $this->classMetaResolver->getProtectedEntityAnnotation($class);
        $schemaName = $protectedAnnotation->name ?: $refClass->getShortName();

        for ($i = 0; $i < count($attributeSchema); ++$i) {
            $attr = $attributeSchema[$i];

            if($attr instanceof Id) {
                unset($attributeSchema[$i]);
            }
        }

        $this->schemaToArrayConverter->loadSchema([
            'class' => $class,
            'attributes'    => $attributeSchema,
            'name'  => $schemaName,
        ]);

        $schema = $this->schemaToArrayConverter->convert();

        return $schema;
    }
}