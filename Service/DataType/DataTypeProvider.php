<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-10
 * Time: 22:27
 */

namespace Vaderlab\EAV\Core\Service\DataType;


class DataTypeProvider
{
    /**
     * @var array
     */
    private $types;

    /**
     * DataTypeProvider constructor.
     * @param array $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * @return array
     */
    public function getTypes(): array
    {
        return array_keys($this->types);
    }

    /**
     * @return array
     */
    public function getTypesConfig(): array
    {
        return $this->types;
    }
}