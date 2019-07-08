<?php


namespace Vaderlab\EAV\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="vaderlab_eav_unique_idx")
 * @ORM\Entity()
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="eav")
 * @ORM\HasLifecycleCallbacks()
 */
class UniqueIndex
{
    use BaseEntityTrait;

    /**
     * @var string
     * @ORM\Column(name="unique_key", unique=true, length=256, nullable=false)
     */
    private $uniqueKey;

    /**
     * @var Entity
     * @ORM\ManyToOne(targetEntity="Vaderlab\EAV\Core\Entity\Entity", fetch="LAZY", inversedBy="uniqueIndexes")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", nullable=false)
     */
    private $entity;

    /**
     * @param string $uniqueKey
     */
    public function setUniqueKey(string $uniqueKey)
    {
        $this->uniqueKey = $uniqueKey;
    }
}