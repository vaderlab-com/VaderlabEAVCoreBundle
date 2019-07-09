<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-06
 * Time: 16:41
 */

namespace Vaderlab\EAV\Core\Entity\ValueType;


use Doctrine\ORM\Mapping as ORM;
use Vaderlab\EAV\Core\Entity\AbstractValue;
use Vaderlab\EAV\Core\Entity\ValueTypeHasDefaultInterface;

/**
 * Class ValueInteger
 * @package Vaderlab\EAV\Core\Entity\ValueType
 * @ORM\Table(name="vaderlab_eav_value_integer")
 * @ORM\Entity()
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="eav")
 */
class ValueInteger extends AbstractValue implements ValueTypeHasDefaultInterface
{
    /**
     * @var integer
     * @ORM\Column( name="val", type="integer", nullable=true )
     */
    protected $value;

    /**
     * @return string
     */
    public function getCastType(): string
    {
        return 'integer';
    }

    public function __toString(): string
    {
        return sprintf('%d', $this->value );
    }
}