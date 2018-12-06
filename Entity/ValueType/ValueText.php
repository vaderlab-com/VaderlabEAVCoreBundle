<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-06
 * Time: 16:52
 */

namespace Vaderlab\EAV\Entity\ValueType;

use Vaderlab\EAV\Entity\AbstractValue;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ValueString
 * @package Vaderlab\EAV\Entity\ValueType
 * @ORM\Entity()
 */
class ValueText extends AbstractValue
{
    /**
     * @var string
     * @ORM\Column( name="val", type="text", nullable=false )
     */
    protected $value = '';

    public function __toString()
    {
        return $this->value;
    }
}