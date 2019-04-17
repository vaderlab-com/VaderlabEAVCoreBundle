<?php


namespace Vaderlab\EAV\Core\Service\Reflection;


use Psr\Log\LoggerInterface;
use ReflectionClass;
use \ReflectionException;
use \ReflectionObject;
use Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException;
use Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException;

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
}