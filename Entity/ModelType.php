<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-05
 * Time: 01:39
 */

namespace Vaderlab\EAV\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Vaderlab\EAV\Repository\ModelTypeRepository")
 */
class ModelType
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
     * @ORM\OneToMany( targetEntity="Model", mappedBy="type", fetch="LAZY", cascade={"remove"} )
     */
    private $models;

    /**
     * @var Collection
     * @ORM\OneToMany( targetEntity="Attribute", mappedBy="modelType", fetch="EAGER", cascade={"remove", "persist", "merge"} )
     */
    private $attributes;

    /**
     * ModelType constructor.
     */
    public function __construct()
    {
        $this->models = new ArrayCollection();
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
    public function getModels(): Collection
    {
        return $this->models;
    }

    /**
     * @param Collection $models
     * @return $this
     */
    public function setModels(Collection $models): ModelType
    {
        $this->models = $models;

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
    public function setAttributes(Collection $attributes): ModelType
    {
        $this->attributes = $attributes;

        return $this;
    }
}