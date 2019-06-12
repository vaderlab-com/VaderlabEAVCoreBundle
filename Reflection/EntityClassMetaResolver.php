<?php


namespace Vaderlab\EAV\Core\Reflection;


use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Vaderlab\EAV\Core\Annotation\AnnotationHasTarget;
use Vaderlab\EAV\Core\Annotation\Attributes;
use Vaderlab\EAV\Core\Annotation\Attribute;
use Vaderlab\EAV\Core\Annotation\BaseAttribute;
use Vaderlab\EAV\Core\Annotation\Id;
use Vaderlab\EAV\Core\Annotation\ProtectedEntity;
use Vaderlab\EAV\Core\Exception\Service\Reflection\ForeignPropertyException;
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
     * @return Id
     * @throws ForeignPropertyException
     * @throws PropertySchemeInvalidException
     */
    public function getIdProperty(\ReflectionClass $entityClass)
    {
        $fromProperties = $this->getPropertyAnnotation($entityClass, Id::class);
        $fromClass = $this->getAttributesAnnotationData($entityClass);

        $tmp = array_merge($fromProperties, $fromClass);
        $idProp = array_filter($tmp, function($val) {
            return $val instanceof Id;
        });

        $propsCnt = count($idProp);

        if($propsCnt === 1) {
            return $idProp[0];
        }

        $message = $propsCnt === 0 ?
            'Can not found @Id property' :
            'The id property is duplicated'
            ;

        throw new ForeignPropertyException(sprintf('%s at the class %s', $message, $entityClass->getName()));
    }



    /**
     * @param \ReflectionClass $entityClass
     * @return array
     * @throws PropertiesAlreadyDeclaredException
     * @throws PropertySchemeInvalidException
     *
     * @todo: Schema validation - temporary solution! Need to be refactoring.
     */
    public function getProtectedAttributes(\ReflectionClass $entityClass): array
    {
        $classAttrs = $this->getAttributesAnnotationData($entityClass);
        $propsAttr = $this->getAttributeAnnotationData($entityClass);
        $intersections = array_intersect($classAttrs, $propsAttr);
        $className = $entityClass->getName();

        if( count($intersections) ) {
            $props = array_values($intersections);

            throw new PropertiesAlreadyDeclaredException($props, $className);
        }

        $properties = array_merge($classAttrs, $propsAttr);

        $isValidProperties = $this->validatePropertiesTargets($properties);
        /** TODO: Refactoring */
        if($isValidProperties === true) {
           return $properties;
        }

        throw new PropertySchemeInvalidException(
            sprintf('Incorrectly declared property "target" for attributes (%s) on the class "%s"',
                implode(', ', $isValidProperties),
                $className
            )
        );
    }

    /**
     * @param $refClass
     * @return bool
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     */
    public function isProtectedSchema($refClass): bool
    {
        if(is_string($refClass)) {
            $refClass = $this->reflection->createReflectionClass($refClass);
        }

        return !!$this->annotationReader->getClassAnnotation($refClass, ProtectedEntity::class);
    }

    /**
     * @param \ReflectionClass $entityClass
     * @return array
     * @throws PropertySchemeInvalidException
     */
    protected function getAttributeAnnotationData(\ReflectionClass $entityClass)
    {
        return $this->getPropertyAnnotation($entityClass, Attribute::class);
    }

    /**
     * @param \ReflectionClass $entityClass
     * @return array
     * @throws PropertySchemeInvalidException
     */
    protected function getPropertyAnnotation(\ReflectionClass $entityClass, string $annotationClass)
    {
        $properties = $entityClass->getProperties();

        $propsAttr = [];
        foreach($properties as $property) {
            /** @var BaseAttribute $propAttr */
            $propAttr = $this->annotationReader->getPropertyAnnotation($property, $annotationClass);

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
     * @param string $classAnnotation
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
    protected function setAttributeTarget(BaseAttribute $attribute, \ReflectionProperty $target)
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

    /**
     * @param array $attributes
     * @return array|bool
     */
    protected function validatePropertiesTargets(array $attributes)
    {
        $targets = [];
        /** @var BaseAttribute $attribute */
        foreach ($attributes as $attribute) {
            $target = $attribute->target;
            if($target === null) {
                $attribute->target = (string)$attribute;
            }

            $targets[] = $attribute->target;
        }

        $countTargets = array_count_values($targets);
        $filtered = array_filter(
            $countTargets,
            function ($elem) {
                return($elem > 1);
            });

        return !count($filtered) ?: array_keys($filtered);
    }
}