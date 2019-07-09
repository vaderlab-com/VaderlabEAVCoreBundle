<?php


namespace Vaderlab\EAV\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="vaderlab_eav_unique_idx")
 * @ORM\Entity()
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="eav")
 */
class UniqueIndex
{
    use BaseEntityTrait;

    /**
     * @var string
     * @ORM\Column(name="unique_key", unique=true, length=128, nullable=false)
     */
    private $uniqueKey;

    /**
     * @var Entity
     * @ORM\ManyToOne(targetEntity="Vaderlab\EAV\Core\Entity\Entity", fetch="LAZY", inversedBy="uniqueIndexes")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @ORM\Cache("NONSTRICT_READ_WRITE", region="eav")
     */
    private $entity;

    /**
     * @var Attribute
     * @ORM\ManyToOne(targetEntity="Vaderlab\EAV\Core\Entity\Attribute", fetch="LAZY", inversedBy="uniqueIndexes", cascade={"persist", "merge"})
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @ORM\Cache("NONSTRICT_READ_WRITE", region="eav")
     */
    private $attribute;

    /**
     * @param string $uniqueKey
     * @return $this
     */
    public function setUniqueKey(string $uniqueKey): UniqueIndex
    {
        $this->uniqueKey = $uniqueKey;

        return $this;
    }

    /**
     * @return Entity
     */
    public function getEntity(): Entity
    {
        return $this->entity;
    }

    /**
     * @param Entity $entity
     * @return UniqueIndex
     */
    public function setEntity(Entity $entity): UniqueIndex
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return string
     */
    public function getUniqueKey(): string
    {
        return $this->uniqueKey;
    }

    /**
     * @return Attribute
     */
    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    /**
     * @param Attribute $attribute
     * @return UniqueIndex
     */
    public function setAttribute(Attribute $attribute): UniqueIndex
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getUniqueKey() ?: '';
    }
}