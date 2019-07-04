<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-06
 * Time: 16:49
 */

namespace Vaderlab\EAV\Core\Entity\ValueType;


use Vaderlab\EAV\Core\Entity\AbstractValue;
use Doctrine\ORM\Mapping as ORM;
use Vaderlab\EAV\Core\Entity\ValueTypeHasDefaultInterface;

/**
 * Class ValueBoolean
 * @package Vaderlab\EAV\Core\Entity\ValueType
 * @ORM\Entity()
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="eav")
 */
class ValueBoolean extends AbstractValue implements ValueTypeHasDefaultInterface
{
    /**
     * @var boolean
     * @ORM\Column( name="val", type="boolean", nullable=false )
     */
    protected $value = false;

    /**
     * @return string
     */
    public function getCastType(): string
    {
        return 'boolean';
    }

    public function __toString(): string
    {
        return $this->value ? 'true': 'false';
    }
}