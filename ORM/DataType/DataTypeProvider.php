<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-10
 * Time: 22:27
 */

namespace Vaderlab\EAV\Core\ORM\DataType;


use Vaderlab\EAV\Core\Entity\AbstractValue;
use Vaderlab\EAV\Core\Exception\Service\DataType\UnregisteredValueTypeException;

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

    /**
     * @param String $type
     * @return mixed
     * @throws UnregisteredValueTypeException
     */
    public function getValueClass(String $type)
    {
        if(isset($this->types[$type]) === false) {
            throw new UnregisteredValueTypeException($type);
        }

        return $this->types[$type];
    }

    /**
     * @param String $type
     * @return AbstractValue
     * @throws UnregisteredValueTypeException
     */
    public function createValueObject(String $type): AbstractValue
    {
        $class = $this->getValueClass($type);

        return new $class;
    }
}