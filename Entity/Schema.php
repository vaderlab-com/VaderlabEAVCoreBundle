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

/**
 * @ORM\Entity(repositoryClass="Vaderlab\EAV\Core\Repository\SchemaRepository")
 * @ORM\Cache(usage="READ_WRITE", region="model_type_region")
 */
class Schema
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column( name="name", type="string", length=50, nullable=false, unique=true )
     */
    private $name;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany( targetEntity="Entity", mappedBy="type", fetch="LAZY", cascade={"remove"} )
     */
    private $entities;

    /**
     * @var Collection
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     * @ORM\OneToMany( targetEntity="Vaderlab\EAV\Core\Entity\Attribute", mappedBy="Schema", fetch="EAGER", cascade={"remove", "persist", "merge"} )
     */
    private $attributes;

    /**
     * @var Collection
     * @ORM\OneToMany( targetEntity="Vaderlab\EAV\Core\Entity\Form", mappedBy="Schema", fetch="LAZY", cascade={"all"} )
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     */
    private $forms;

    /**
     * Schema constructor.
     */
    public function __construct()
    {
        $this->forms = new ArrayCollection();
        $this->entities = new ArrayCollection();
        $this->attributes = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return $this
     */
    public function setName( ?string $name )
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
     * @return Collection
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
     * @return Collection
     */
    public function getForms(): Collection
    {
        return $this->forms;
    }

    /**
     * @param Collection $forms
     * @return Schema
     */
    public function setForms(Collection $forms): Schema
    {
        $this->forms = $forms;

        return $this;
    }
}