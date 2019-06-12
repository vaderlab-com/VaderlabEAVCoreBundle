<?php


namespace Vaderlab\EAV\Core\Schema\Discover\Database;


use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Schema\Discover\AttributeToArrayConverter as BaseAttributeConverter;

class AttributeToArrayConverter extends BaseAttributeConverter
{

    /**
     * @param Attribute $attribute
     * @return string
     */
    protected function convertName($attribute): string
    {
        return $attribute->getName();
    }

    /**
     * @param Attribute $attribute
     * @return mixed
     */
    protected function convertDefaultValue($attribute)
    {
        return $attribute->getDefaultValue();
    }

    /**
     * @param Attribute $attribute
     * @return string
     */
    protected function convertType($attribute): string
    {
        return $attribute->getType();
    }

    /**
     * @param Attribute $attribute
     * @return bool
     */
    protected function convertNullable($attribute): bool
    {
        return $attribute->isNullable();
    }

    /**
     * @param Attribute $attribute
     * @return bool
     */
    protected function convertIndexable($attribute): bool
    {
        return $attribute->isIndexable();
    }

    /**
     * @param Attribute $attribute
     * @return bool
     */
    protected function convertUnique($attribute): bool
    {
        return $attribute->isUnique();
    }

    /**
     * @param Attribute $attribute
     * @return string|null
     */
    protected function convertDescription($attribute): ?string
    {
        return $attribute->getDescription();
    }

    /**
     * @param Attribute $attribute
     * @return int|null
     */
    protected function convertLength($attribute): ?int
    {
        return $attribute->getLength();
    }
}