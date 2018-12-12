<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 3.12.18
 * Time: 15.36
 */

namespace Vaderlab\EAV\Core\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Vaderlab\EAV\Core\Repository\AttributeRepository")
 * @ORM\Cache(usage="READ_WRITE", region="attribute_region")
 * @ORM\Table(name="Attribute", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="attribute", columns={"name", "schema_id"})
 * })
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
     * @ORM\Column( name="name", type="string", length=50, nullable=false )
     */
    private $name;

    /**
     * @var String - string, integer, float, text, boolean, spatial ( Polygon, Polyline, etc… ), etc…
     * @ORM\Column( name="type", type="string", length=50, nullable=false )
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
     * @var Schema
     * @ORM\ManyToOne( targetEntity="Schema", fetch="LAZY" )
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     */
    private $schema;

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
     * @return Schema
     */
    public function getSchema(): ?Schema
    {
        return $this->schema;
    }

    /**
     * @param Schema $Schema
     * @return Attribute
     */
    public function setEntity(Schema $Schema ): Attribute
    {
        $this->schema = $Schema;

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