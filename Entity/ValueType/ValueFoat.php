<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-06
 * Time: 16:46
 */

namespace Vaderlab\EAV\Entity\ValueType;


use Vaderlab\EAV\Entity\AbstractValue;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ValueFoat
 * @package Vaderlab\EAV\Entity\ValueType
 * @ORM\Entity()
 */
class ValueFoat extends AbstractValue
{
    /**
     * @var float
     * @ORM\Column( name="val", type="float", nullable=true )
     */
    protected $value;

    public function __toString()
    {
        return sprintf('%f', $this->value );
    }
}