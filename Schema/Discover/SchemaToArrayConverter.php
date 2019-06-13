<?php


namespace Vaderlab\EAV\Core\Schema\Discover;


abstract class SchemaToArrayConverter
{
    /**
     * @var AttributeToArrayConverter
     */
    private $attributeConverter;

    /**
     * @var mixed
     */
    private $schema;

    /**
     * SchemaToArrayConverter constructor.
     * @param AttributeToArrayConverter $converter
     */
    public function __construct(AttributeToArrayConverter $converter)
    {
        $this->attributeConverter = $converter;
    }

    /**
     * @return string
     */
    protected abstract function getName(): string;

    /**
     * @return string
     */
    protected abstract function getClassname(): string;

    /**
     * @return array
     */
    protected abstract function getAttributes(): array;

    /**
     * @return array
     */
    public function convert(): array
    {
        return [
            'name'  => $this->getName(),
            'class' => $this->getClassname(),
            'attributes'    => $this->convertAttributes(),
        ];
    }

    /**
     * @return array
     */
    protected function convertAttributes(): array
    {
        $result = [];
        $attributes = $this->getAttributes();

        foreach ($attributes as $attribute)
        {
            $result[] = $this->attributeConverter->convert($attribute);
        }

        return $result;
    }

    /**
     * @param $schema
     */
    public function loadSchema($schema): void
    {
        $this->schema = $schema;
    }

    /**
     * @return mixed
     */
    public function getSchema()
    {
        return $this->schema;
    }
}