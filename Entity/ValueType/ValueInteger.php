<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-06
 * Time: 16:41
 */

namespace Vaderlab\EAV\Entity\ValueType;


use Doctrine\ORM\Mapping as ORM;
use Vaderlab\EAV\Entity\AbstractValue;

/**
 * Class ValueInteger
 * @package Vaderlab\EAV\Entity\ValueType
 * @ORM\Entity()
 * @ORM\Cache(usage="READ_WRITE", region="value_region")
 */
class ValueInteger extends AbstractValue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var integer
     * @ORM\Column( name="val", type="integer", nullable=true )
     */
    protected $value;

    public function __toString()
    {
        return sprintf('%d', $this->value );
    }
}