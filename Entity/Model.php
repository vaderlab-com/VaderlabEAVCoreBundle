<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 3.12.18
 * Time: 15.34
 */

namespace Vaderlab\EAV\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Vaderlab\EAV\Repository\ModelRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Model
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
     * @var ModelType
     * @ORM\ManyToOne( targetEntity="ModelType", inversedBy="model", fetch="EAGER", cascade={"persist", "merge", "refresh"} )
     */
    private $type;

    /**
     * @var Collection[]
     * @ORM\OneToMany( targetEntity="AbstractValue", mappedBy="model", cascade={"all"} )
     */
    private $values;

    /**
     * Model constructor.
     */
    public function __construct()
    {
        $this->type = new ModelType();
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
    public function getCreatedAt(): \DateTime
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
     * @return ModelType
     */
    public function getType(): ?ModelType
    {
        return $this->type;
    }

    /**
     * @param ModelType $type
     */
    public function setType(ModelType $type): void
    {
        $this->type = $type;
    }

    /**
     * @return Collection[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param Collection[] $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }
}