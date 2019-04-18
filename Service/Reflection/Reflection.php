<?php


namespace Vaderlab\EAV\Core\Service\Reflection;


use \ReflectionClass;
use \ReflectionProperty;
use \ReflectionException;
use \ReflectionObject;
use Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException;
use Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException;
use Vaderlab\EAV\Core\Exception\Service\Reflection\ForeignKeyBindException;
use Vaderlab\EAV\Core\Exception\Service\Reflection\PropertyNotExistsException;

class Reflection
{
    const FOREIGN_PROPERTY = 'id';

    /**
     * @param string $class
     * @return object
     * @throws EntityClassBindException
     * @throws EntityClassNotExistsException
     */
    public function createObject(string $class): object
    {
        if(!class_exists($class)) {
            throw new EntityClassNotExistsException($class);
        }

        try {
            $refClass = new ReflectionClass($class);
        } catch ( ReflectionException $e ) {
            throw new EntityClassBindException($class, 0, $e);
        }

        $refObject = $refClass->newInstanceWithoutConstructor();

        return $refObject;
    }

    /**
     * @param object $entityObject
     * @return ReflectionObject
     */
    public function createReflectionObject(object $entityObject)
    {
        return new ReflectionObject($entityObject);
    }

    /**
     * @param object $object
     * @param ReflectionObject $refObject
     * @param string $attribute
     * @param $value
     * @throws ForeignKeyBindException
     * @throws ReflectionException
     */
    public function setReflectionAttributeValue(object $object, ReflectionObject $refObject, string $attribute, $value): void
    {
        $property = $this->getReflectionProperty($refObject, $attribute);
        if($property === null) {
            throw new ForeignKeyBindException($attribute, get_class($object));
        }

        $property->setValue($object, $value);
    }

    /**
     * @param ReflectionObject $refObject
     * @param object $object
     * @param string $attribute
     * @return mixed
     * @throws PropertyNotExistsException
     * @throws ReflectionException
     */
    public function getReflectionAttributeValue(ReflectionObject $refObject, object $object, string $attribute)
    {
        $property = $this->getReflectionProperty($refObject, $attribute);
        if($property === null) {
            throw new PropertyNotExistsException($attribute, get_class($object));
        }

        return $property->getValue($object);
    }

    /**
     * @param ReflectionObject $reflectionObject
     * @param string $attribute
     * @return ReflectionProperty
     * @throws ReflectionException
     */
    protected function getReflectionProperty(ReflectionObject $reflectionObject, string $attribute): ?ReflectionProperty
    {
        if(!$reflectionObject->hasProperty($attribute)) {
            return null;
        }

        $property = $reflectionObject->getProperty($attribute);

        if(!$property->isPublic()) {
            $property->setAccessible(true);
        }

        return $property;
    }
}