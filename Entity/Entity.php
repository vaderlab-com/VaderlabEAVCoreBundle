<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 3.12.18
 * Time: 15.34
 */

namespace Vaderlab\EAV\Core\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="EntityRepository")
 * @ORM\Cache(usage="READ_WRITE", region="eav_entity_region")
 * @ORM\HasLifecycleCallbacks()
 */
class Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column( name="created_at", type="datetime", nullable=false )
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     * @ORM\Column( name="updated_at", type="datetime", nullable=true )
     */
    private $updatedAt;

    /**
     * @var Schema
     * @ORM\ManyToOne( targetEntity="Schema", inversedBy="model", fetch="EAGER", cascade={"persist", "merge", "refresh"} )
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     */
    private $type;

    /**
     * @var Collection
     * @ORM\OneToMany( targetEntity="Vaderlab\EAV\Core\Entity\AbstractValue", mappedBy="entity", cascade={"all"} )
     * @ORM\Cache("READ_WRITE")
     */
    protected $values;

    /**
     * Model constructor.
     */
    public function __construct()
    {
        $this->type = new Schema();
        $this->values = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist()
     * @throws \Exception
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     * @throws \Exception
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @return Schema
     */
    public function getType(): ?Schema
    {
        return $this->type;
    }

    /**
     * @return Collection[]
     */
    public function getValues(): Collection
    {
        return $this->values;
    }

    /**
     * @param Collection[] $values
     */
    public function setValues(Collection $values): void
    {
        $this->values = $values;
    }

    public function getValue( string $name )
    {
        $attributes = $this->getType()->getAttributes();
    }
}