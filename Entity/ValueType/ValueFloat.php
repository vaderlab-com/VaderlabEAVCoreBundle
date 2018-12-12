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
 * @ORM\Entity()
 * @ORM\Cache(usage="READ_WRITE", region="value_region")
 */
class ValueFloat extends AbstractValue implements ValueTypeHasDefaultInterface
{
    use ValueTypeHasDefaultTrait;

    /**
     * @var float
     * @ORM\Column( name="val", type="float", nullable=true )
     */
    protected $value;

    /**
     * @return string
     */
    protected function getCastType(): string
    {
        return 'float';
    }

    public function __toString()
    {
        return sprintf('%f', $this->value );
    }
}