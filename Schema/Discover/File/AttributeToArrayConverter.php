<?php


namespace Vaderlab\EAV\Core\Schema\Discover\File;


use Vaderlab\EAV\Core\Annotation\Attribute;
use \Vaderlab\EAV\Core\Schema\Discover\AttributeToArrayConverter as BaseAttributeConverter;

class AttributeToArrayConverter extends BaseAttributeConverter
{

    /**
     * @param Attribute $attribute
     * @return string
     */
    protected function convertName($attribute): string
    {
        return $attribute->name;
    }

    /**
     * @param Attribute $attribute
     * @return mixed
     */
    protected function convertDefaultValue($attribute)
    {
        return $attribute->default;
    }

    /**
     * @param Attribute $attribute
     * @return string
     */
    protected function convertType($attribute): string
    {
        return $attribute->type;
    }

    /**
     * @param Attribute $attribute
     * @return bool
     */
    protected function convertNullable($attribute): bool
    {
        return $attribute->nullable;
    }

    /**
     * @param Attribute $attribute
     * @return bool
     */
    protected function convertIndexable($attribute): bool
    {
        return $attribute->indexable;
    }

    /**
     * @param Attribute $attribute
     * @return bool
     */
    protected function convertUnique($attribute): bool
    {
        return $attribute->unique;
    }

    /**
     * @param Attribute $attribute
     * @return string|null
     */
    protected function convertDescription($attribute): ?string
    {
        return $attribute->description;
    }

    /**
     * @param Attribute $attribute
     * @return int|null
     */
    protected function convertLength($attribute): ?int
    {
        return $attribute->length;
    }
}