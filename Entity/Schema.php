<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-05
 * Time: 01:39
 */

namespace Vaderlab\EAV\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vaderlab\EAV\Core\Model\SchemaInterface;

/**
 * @ORM\Table(name="vaderlab_eav_schema")
 * @ORM\Entity(repositoryClass="Vaderlab\EAV\Core\Repository\SchemaRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="eav")
 */
class Schema implements SchemaInterface
{
    use BaseEntityTrait;

    /**
     * @var string
     * @ORM\Column(
     *     name="entity_class",
     *     type="string",
     *     length=150,
     *     nullable=true,
     *     unique=true
     * )
     */
    private $entityClass = null;

    /**
     * @var string
     * @ORM\Column(
     *     name="name",
     *     type="string",
     *     length=50,
     *     nullable=false,
     *     unique=true
     *     )
     * @Assert\Regex(
     *     match=true,
     *     pattern="/^[a-z0-9\-\_]+$/i",
     *     message="Schema name is not valid."
     * )
     */
    private $name = '';

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(
     *     targetEntity="Entity",
     *     mappedBy="schema",
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=false,
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="id", referencedColumnName="schema_id", nullable=false, onDelete="CASCADE")
     */
    private $entities;

    /**
     * @var Collection
     * @ORM\Cache("NONSTRICT_READ_WRITE", region="eav")
     * @ORM\OneToMany(
     *     targetEntity="Vaderlab\EAV\Core\Entity\Attribute",
     *     mappedBy="schema",
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=false,
     *     cascade={"persist"}
     *     )
     */
    private $attributes;

    /**
     * Schema constructor.
     */
    public function __construct()
    {
        $this->entities = new ArrayCollection();
        $this->attributes = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return $this
     */
    public function setName(string $name): Schema
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getEntities(): Collection
    {
        return $this->entities;
    }

    /**
     * @param Collection $entities
     * @return $this
     */
    public function setEntities(Collection $entities): Schema
    {
        $this->entities = $entities;

        return $this;
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    /**
     * @param Collection $attributes
     * @return $this
     */
    public function setAttributes(Collection $attributes): Schema
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return !!$this->getAttribute($name);
    }

    /**
     * @param string $name
     * @return Attribute|null
     */
    public function getAttribute(string $name): ?Attribute
    {
        $attr = $this->attributes->filter(function (Attribute $attribute) use ($name) {
            return $name === $attribute->getName();
        });

        $attribute = $attr->first();

        if(!($attribute instanceof Attribute)) {
            return null;
        }

        return $attribute;
    }

    public function __toString()
    {
        return sprintf('%s [%d]', $this->name, $this->id);
    }

    /**
     * @return string
     */
    public function getEntityClass(): ?string
    {
        return $this->entityClass;
    }

    /**
     * @param string|null $entityClass
     * @return Schema
     */
    public function setEntityClass(?string $entityClass): Schema
    {
        $this->entityClass = $entityClass;

        return $this;
    }
}