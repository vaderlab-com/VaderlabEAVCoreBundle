<?php


namespace Vaderlab\EAV\Core\Reflection;


use Doctrine\Common\Annotations\AnnotationReader;
use Vaderlab\EAV\Core\Annotation\Attributes;
use Vaderlab\EAV\Core\Annotation\Attribute;
use Vaderlab\EAV\Core\Exception\Service\Reflection\PropertiesAlreadyDeclaredException;
use Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException;

class EntityClassMetaResolver
{
    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * @var Reflection
     */
    private $reflection;

    /**
     * EntityClassMetaResolver constructor.
     * @param AnnotationReader $reader
     * @param Reflection $reflection
     */
    public function __construct(
        AnnotationReader $reader,
        Reflection $reflection
    ) {
        $this->annotationReader = $reader;
        $this->reflection = $reflection;
    }

    /**
     * @param \ReflectionClass $entityClass
     * @return array
     * @throws PropertiesAlreadyDeclaredException
     * @throws PropertySchemeInvalidException
     */
    public function getProtectedAttributes(\ReflectionClass $entityClass): array
    {
        $classAttrs = $this->getAttributesAnnotationData($entityClass);
        $propsAttr = $this->getAttributeAnnotationData($entityClass);

        $intersections = array_intersect($classAttrs, $propsAttr);

        if( count($intersections) ) {
            $props = array_values($intersections);
            $class = $entityClass->getName();

            throw new PropertiesAlreadyDeclaredException($props, $class);
        }

        return array_merge($classAttrs, $propsAttr);
    }

    /**
     * @param \ReflectionObject $entityClass
     * @return string
     */
    public function getAttributesContainerPropertyName(\ReflectionObject $entityClass): string
    {
        /** @var Attributes $aObj */
        $aObj =  $this->annotationReader->getPropertyAnnotation($entityClass, Attributes::class);

        return $aObj;
    }

    /**
     * @param \ReflectionClass $entityClass
     * @return array
     * @throws PropertySchemeInvalidException
     */
    protected function getAttributeAnnotationData(\ReflectionClass $entityClass)
    {
        $properties = $entityClass->getProperties();

        $propsAttr = [];
        foreach($properties as $property) {
            /** @var Attribute $propAttr */
            $propAttr = $this->annotationReader->getPropertyAnnotation($property, Attribute::class);

            if( $propAttr === null ) {
                continue;
            }

            $this->setAttributeTarget($propAttr, $property);
            $propsAttr[] = $propAttr;
        }

        return $propsAttr;
    }

    /**
     * @param \ReflectionClass $entityClass
     * @return array
     */
    protected function getAttributesAnnotationData(\ReflectionClass $entityClass)
    {
        $classAttrObject = $this->annotationReader->getClassAnnotation($entityClass, Attributes::class);
        $classAttrs = $classAttrObject ? $classAttrObject->attributes : [];

        if(!is_array($classAttrs)) {
            $classAttrs = [$classAttrs];
        }

        return $classAttrs;
    }

    /**
     * @param Attribute $attribute
     * @param \ReflectionProperty $target
     * @throws PropertySchemeInvalidException
     */
    protected function setAttributeTarget(Attribute $attribute, \ReflectionProperty $target)
    {
        $tmpT = $attribute->target;
        $propertyName = $target->getName();
        if($tmpT !== null && $tmpT !== $propertyName) {
            throw new PropertySchemeInvalidException(
                sprintf( 'Incorrectly declared property "target" for attribute %s on the class %s',
                    $attribute->name,
                    $target->getDeclaringClass()->getName()
                    )
            );
        }

        $attribute->target = $propertyName;
    }
}