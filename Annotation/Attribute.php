<?php


namespace Vaderlab\EAV\Core\Annotation;


use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\ORM\Mapping\Annotation;

/**
 * Class Entity
 * @package Vaderlab\EAV\Core\Annotation
 *
 * @Annotation
 * @Target({"ANNOTATION", "PROPERTY"})
 */
class Attribute extends BaseAttribute
{
    public $target;

    /**
     * @var string
     */
    public $name;

    /**
     * @var integer|null
     */
    public $length = null;

    /**
     * @var string
     */
    public $type;

    /**
     * @var boolean
     */
    public $nullable = false;
    /**
     * @var boolean
     */
    public $indexable = false;

    /**
     * @var boolean
     */
    public $unique = false;
    /**
     * @var string
     */
    public $description = null;

    /**
     * @var mixed
     */
    public $default = null;

    /**
     * Attribute constructor.
     * @param array|null $property
     */
    public function __construct(array $property)
    {
        foreach ($property as $pName => $value) {
            $this->{$pName} = $value;
        }
    }

    public function __toString(): string
    {
        return $this->name;
    }
}