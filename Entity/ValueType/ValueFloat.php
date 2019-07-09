<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-06
 * Time: 16:46
 */

namespace Vaderlab\EAV\Core\Entity\ValueType;


use Vaderlab\EAV\Core\Entity\AbstractValue;
use Doctrine\ORM\Mapping as ORM;
use Vaderlab\EAV\Core\Entity\ValueTypeHasDefaultInterface;

/**
 * Class ValueFoat
 * @package Vaderlab\EAV\Core\Entity\ValueType
 * @ORM\Table(name="vaderlab_eav_value_float")
 * @ORM\Entity()
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="eav")
 */
class ValueFloat extends AbstractValue implements ValueTypeHasDefaultInterface
{
    /**
     * @var float
     * @ORM\Column( name="val", type="float", nullable=true )
     */
    protected $value;

    /**
     * @return string
     */
    public function getCastType(): string
    {
        return 'float';
    }

    public function __toString(): string
    {
        return sprintf('%f', $this->value );
    }
}