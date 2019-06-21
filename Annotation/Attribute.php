<?php


namespace Vaderlab\EAV\Core\Annotation;


use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\ORM\Mapping\Annotation;
use Vaderlab\EAV\Core\Model\AttributeInterface;

/**
 * Class Entity
 * @package Vaderlab\EAV\Core\Annotation
 *
 * @Annotation
 * @Target({"ANNOTATION", "PROPERTY"})
 */
class Attribute extends BaseAttribute implements AttributeInterface
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

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * @return bool
     */
    public function isIndexable(): bool
    {
       return $this->indexable;
    }

    /**
     * @return int
     */
    public function getLength(): ?int
    {
        return $this->length;
    }

    /**
     * @return String
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return String
     */
    public function getDefaultValue(): ?String
    {
        return $this->default;
    }

    /**
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->unique;
    }
}