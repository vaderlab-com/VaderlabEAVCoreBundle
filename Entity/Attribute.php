<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 3.12.18
 * Time: 15.36
 */

namespace Vaderlab\EAV\Core\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="vaderlab_eav_attribute")
 * @ORM\Entity(repositoryClass="Vaderlab\EAV\Core\Repository\AttributeRepository")
 * @ORM\Cache(usage="READ_WRITE", region="attribute_region")
 * @ORM\Table(name="Attribute",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="attribute", columns={"name", "schema_id"})
 * })
 */
class Attribute
{
    use BaseEntityTrait;

    /**
     * @var String
     * @ORM\Column(
     *     name="name",
     *     type="string",
     *     length=50,
     *     nullable=false
     *     )
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(
     *     name="slug",
     *     type="string",
     *     length=50,
     *     nullable=false
     *     )
     * @Assert\Regex(
     *     match=true,
     *     pattern="/^[a-z0-9\-\_]+$/i",
     *     message="The slug is not valid."
     * )
     */
    private $slug = '';
    /**
     * @var String - string, integer, float, text, boolean, spatial ( Polygon, Polyline, etc… ), etc…
     * @ORM\Column(
     *     name="type",
     *     type="string",
     *     length=50,
     *     nullable=false
     *     )
     */
    private $type = '';

    /**
     * @var boolean
     * @ORM\Column(
     *     name="nullable",
     *     type="boolean",
     *     nullable=false
     *     )
     */
    private $nullable = true;

    /**
     * @var boolean
     * @ORM\Column(
     *     name="indexable",
     *     type="boolean",
     *     nullable=false
     * )
     */
    private $indexable = false;

    /**
     * @var boolean
     * @ORM\Column(
     *     name="is_unique",
     *     type="boolean",
     *     nullable=false
     *     )
     */
    private $isUnique = false;
    /**
     * @var integer
     * @ORM\Column(
     *     name="length",
     *     type="integer",
     *     nullable=true
     *     )
     */
    private $length;

    /**
     * @var Schema
     * @ORM\ManyToOne(
     *     targetEntity="Schema",
     *     fetch="LAZY",
     *     inversedBy="attributes"
     * )
     * @ORM\JoinColumn(name="schema_id", referencedColumnName="id", nullable=false)
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     */
    private $schema;

    /**
     * @var String
     * @ORM\Column( name="description", type="text", nullable=true )
     */
    private $description = '';

    /**
     * @var String
     * @ORM\Column( name="default_value", type="string", length=256, nullable=true)
     */
    private $defaultValue;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }


    public function setName( string $name ): Attribute
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
    public function setSchema(Schema $Schema ): Attribute
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
    public function getLength(): ?int
    {
        return $this->length;
    }

    /**
     * @param int $length
     * @return $this
     */
    public function setLength(?int $length = null): Attribute
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @return String
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param String $description
     * @return $this
     */
    public function setDescription(?string $description = ''): Attribute
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param String $type
     * @return Attribute
     */
    public function setType(String $type): Attribute
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return String
     */
    public function getDefaultValue(): ?String
    {
        return $this->defaultValue;
    }

    /**
     * @param String $defaultValue
     * @return Attribute
     */
    public function setDefaultValue(String $defaultValue = ''): Attribute
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->isUnique;
    }

    /**
     * @param bool $isUnique
     * @return Attribute
     */
    public function setIsUnique(bool $isUnique): Attribute
    {
        $this->isUnique = $isUnique;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return Attribute
     */
    public function setSlug(string $slug): Attribute
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $message = sprintf('%s [%s]', $this->getName(), $this->getType());

        return $message . ($this->length ? sprintf(' (%d) ', $this->length) : '');
    }
}