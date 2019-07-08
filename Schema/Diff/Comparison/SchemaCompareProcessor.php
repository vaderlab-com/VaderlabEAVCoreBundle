<?php


namespace Vaderlab\EAV\Core\Schema\Diff\Comparison;


use Symfony\Component\Routing\Tests\Matcher\DumpedUrlMatcherTest;
use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Entity\Schema;
use Vaderlab\EAV\Core\Model\AttributeInterface;
use Vaderlab\EAV\Core\Model\SchemaInterface;

class SchemaCompareProcessor extends AbstractCompareProcessor
{

    private $attributeCompareProcessor;

    public function __construct(
        AttributeCompareProcessor $attributeCompareProcessor,
        PropertyProxyFactory $propertyProxyFactory
    ) {
        parent::__construct($propertyProxyFactory);
        $this->attributeCompareProcessor    = $attributeCompareProcessor;
    }

    protected function compareAttributes(Schema $source, SchemaInterface $dest, bool $apply): array
    {
        $diff = [];
        $attrSource = $source->getAttributes();
        $attrsDest  = $dest->getAttributes();
        $currentAttrName = null;

        $filter = function(AttributeInterface $attribute) use (&$currentAttrName){
            return $attribute->getName() === $currentAttrName;
        };

        /** @var AttributeInterface $newAttr */
        foreach ($attrsDest as $newAttr) {
            $currentAttrName = $newAttr->getName();
            $model = $attrSource->filter($filter)->first();

            /** @TODO: temporary solution*/
            if(!$model) {
                $model = new Attribute();
                $model->setSchema($source);
                $model->setName($currentAttrName);
                $source->getAttributes()->add($model);
            }

            $tmpDiff = $this->attributeCompareProcessor->process($model, $newAttr, $apply);
            if(!$tmpDiff || !count($tmpDiff)) {
                continue;
            }

            $diff[$currentAttrName] = $tmpDiff;
        }

        return $diff;
    }

    /**
     * @param \Vaderlab\EAV\Core\Model\SchemaInterface $source
     * @param \Vaderlab\EAV\Core\Model\SchemaInterface $dest
     * @param bool $apply
     * @return array
     */
    public function process($source, $dest, bool $apply = false): array
    {
        $result     = [];
        $properties = parent::process($source, $dest, $apply);
        $attributes = $this->compareAttributes($source, $dest, $apply);
        if(count($properties)) {
            $result = $properties;
        }

        if(count($attributes)) {
            $result['attributes'] = $attributes;
        }

        return $result;
    }
}