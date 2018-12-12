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

/**
 * Class ValueBoolean
 * @package Vaderlab\EAV\Core\Entity\ValueType
 * @ORM\Entity()
 * @ORM\Cache(usage="READ_WRITE", region="value_region")
 */
class ValueBoolean extends AbstractValue
{
    /**
     * @var boolean
     * @ORM\Column( name="val", type="boolean", nullable=false )
     */
    protected $value = false;

    public function __toString()
    {
        return $this->value ? 'true': 'false';
    }
}