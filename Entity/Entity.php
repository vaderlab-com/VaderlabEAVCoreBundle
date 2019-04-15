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
 * @ORM\Table(name="vaderlab_eav_entity")
 * @ORM\Entity(repositoryClass="Vaderlab\EAV\Core\Repository\EntityRepository")
 * @ORM\Cache(usage="READ_WRITE", region="eav_entity_region")
 * @ORM\HasLifecycleCallbacks()
 */
class Entity implements EAVEntityInterface
{
    use BaseEntityTrait;

    /**
     * @var \DateTime
     * @ORM\Column(
     *     name="created_at",
     *     type="datetime",
     *     nullable=false
     *     )
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     * @ORM\Column(
     *     name="updated_at",
     *     type="datetime",
     *     nullable=true
     *     )
     */
    private $updatedAt;

    /**
     * @var Schema
     * @ORM\ManyToOne(
     *     targetEntity="Schema",
     *     inversedBy="entities",
     *     fetch="EAGER",
     *     cascade={"persist", "merge", "refresh"}
     *     )
     * @ORM\JoinColumn(
     *     name="schema_id",
     *     referencedColumnName="id",
     *     nullable=false
     *     )
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     */
    private $schema;

    /**
     * @var Collection
     * @ORM\OneToMany(
     *     targetEntity="Vaderlab\EAV\Core\Entity\AbstractValue",
     *     mappedBy="entity",
     *     cascade={"all"}
     *     )
     * @ORM\Cache("READ_WRITE")
     */
    protected $values;

    /**
     * Model constructor.
     */
    public function __construct()
    {
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
    public function getSchema(): ?Schema
    {
        return $this->schema;
    }

    /**
     * @param Schema $schema
     * @return Entity
     */
    public function setSchema(Schema $schema): Entity
    {
        $this->schema = $schema;
        return $this;
    }

    /**
     * @return ArrayAccess[]
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
}