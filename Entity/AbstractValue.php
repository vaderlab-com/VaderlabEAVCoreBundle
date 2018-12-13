<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-06
 * Time: 16:33
 */

namespace Vaderlab\EAV\Core\Entity;


use Doctrine\ORM\Mapping as ORM;
use Vaderlab\EAV\Core\Entity\Entity;

/**
 * Class AbstractValue
 * @package Vaderlab\EAV\Core\Entity\ValueType
 * @ORM\Entity()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="value_type", type="integer")
 * @ORM\DiscriminatorMap({
 *     1 = "Vaderlab\EAV\Core\Entity\ValueType\ValueBoolean",
 *     2 = "Vaderlab\EAV\Core\Entity\ValueType\ValueInteger",
 *     3 = "Vaderlab\EAV\Core\Entity\ValueType\ValueFloat",
 *     4 = "Vaderlab\EAV\Core\Entity\ValueType\ValueString",
 *     5 = "Vaderlab\EAV\Core\Entity\ValueType\ValueText"
 * })
 */
abstract class AbstractValue implements ValueInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;


    /**
     * @var Entity
     * @ORM\ManyToOne(
     *     targetEntity="Vaderlab\EAV\Core\Entity\Entity",
     *     inversedBy="values",
     *     fetch="EXTRA_LAZY",
     *     cascade={"persist", "merge"}
     *     )
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", nullable=false)
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     */
    protected $entity;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var Attribute
     * @ORM\ManyToOne(
     *     targetEntity="Vaderlab\EAV\Core\Entity\Attribute",
     *     fetch="LAZY",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="id", nullable=false)
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     */
    protected $attribute;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $value
     * @return ValueInterface
     */
    public function setValue($value): ValueInterface
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return Entity
     */
    public function getEntity(): ?Entity
    {
        return $this->entity;
    }

    /**
     * @param Entity $entity
     * @return ValueInterface
     */
    public function setEntity(Entity $entity): ValueInterface
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return Attribute
     */
    public function getAttribute(): ?Attribute
    {
        return $this->attribute;
    }

    /**
     * @param Attribute $attribute
     * @return $this
     */
    public function setAttribute(Attribute $attribute): ValueInterface
    {
        $this->attribute = $attribute;

        return $this;
    }
}