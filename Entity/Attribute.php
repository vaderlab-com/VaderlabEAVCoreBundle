<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 3.12.18
 * Time: 15.36
 */

namespace Vaderlab\EAV\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Vaderlab\EAV\Repository\AttributeRepository")
 */
class Attribute
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var String
     */
    private $name;

    /**
     * @var String - string, integer, float, text, boolean, spatial ( Polygon, Polyline, etc… ), etc…
     * @ORM\ManyToOne(  )
     */
    private $type;

    /**
     * @var boolean
     * @ORM\Column( name="nullable", type="boolean", nullable=false )
     */
    private $nullable;

    /**
     * @var boolean
     * @ORM\Column( name="indexable", type="boolean", nullable=false )
     */
    private $indexable;

    /**
     * @var integer
     * @ORM\Column( name="length", type="integer", nullable=false )
     */
    private $length;

    /**
     * @var ModelType
     * @ORM\ManyToOne( targetEntity="ModelType", fetch="LAZY" )
     */
    private $modelType;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }


    public function setName( string $name ): ?string
    {

        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return ModelType
     */
    public function getModelType(): ?ModelType
    {
        return $this->modelType;
    }

    /**
     * @param ModelType $modelType
     * @return Attribute
     */
    public function setModel( ModelType $modelType ): Attribute
    {
        $this->modelType = $modelType;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * @param bool $nullable
     * @return $this
     */
    public function setNullable(bool $nullable): Attribute
    {
        $this->nullable = $nullable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIndexable(): bool
    {
        return $this->indexable;
    }

    /**
     * @param bool $indexable
     * @return $this
     */
    public function setIndexable(bool $indexable): Attribute
    {
        $this->indexable = $indexable;

        return $this;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @param int $length
     * @return $this
     */
    public function setLength(int $length): Attribute
    {
        $this->length = $length;

        return $this;
    }
}