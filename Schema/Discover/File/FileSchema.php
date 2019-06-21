<?php


namespace Vaderlab\EAV\Core\Schema\Discover\File;


use Doctrine\Common\Collections\Collection;
use Vaderlab\EAV\Core\Model\AttributeInterface;
use Vaderlab\EAV\Core\Model\SchemaInterface;

class FileSchema implements SchemaInterface
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $class;

    /**
     * @var Collection
     */
    private $attributes;

    /**
     * FileSchema constructor.
     * @param string $name
     * @param string $class
     * @param Collection $attributes
     */
    public function __construct(string $name, string $class, Collection $attributes)
    {
        $this->name = $name;
        $this->class = $class;
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEntityClass(): ?string
    {
        return $this->class;
    }

    /**
     * @return Collection<AttributeInterface>
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }
}