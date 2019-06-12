<?php


namespace Vaderlab\EAV\Core\Schema\Discover;


abstract class AttributeToArrayConverter
{
    /**
     * @param $attribute
     * @return array
     */
    public function convert($attribute): array
    {
        return [
            'name'          => $this->convertName($attribute),
            'defaultValue'  => $this->convertDefaultValue($attribute),
            'type'          => $this->convertType($attribute),
            'length'        => $this->convertLength($attribute),
            'nullable'      => $this->convertNullable($attribute),
            'indexable'     => $this->convertIndexable($attribute),
            'unique'        => $this->convertUnique($attribute),
            'description'   => $this->convertDescription($attribute),
        ];
    }

    /**
     * @param $attribute
     * @return string
     */
    protected abstract function convertName($attribute): string;

    /**
     * @param $attribute
     * @return mixed
     */
    protected abstract function convertDefaultValue($attribute);

    /**
     * @param $attribute
     * @return string
     */
    protected abstract function convertType($attribute): string;

    /**
     * @param $attribute
     * @return bool
     */
    protected abstract function convertNullable($attribute): bool ;

    /**
     * @param $attribute
     * @return bool
     */
    protected abstract function convertIndexable($attribute): bool ;

    /**
     * @param $attribute
     * @return bool
     */
    protected abstract function convertUnique($attribute): bool ;

    /**
     * @param $attribute
     * @return string|null
     */
    protected abstract function convertDescription($attribute): ?string ;

    /**
     * @param $attribute
     * @return int|null
     */
    protected abstract function convertLength($attribute): ?int;
}