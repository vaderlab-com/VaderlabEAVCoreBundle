<?php


namespace Vaderlab\EAV\Core\Entity\ValueType;


use Vaderlab\EAV\Core\Entity\AbstractValue;
use Vaderlab\EAV\Core\Entity\ValueTypeHasDefaultInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ValueDate
 * @package Vaderlab\EAV\Core\Entity\ValueType
 * @ORM\Entity()
 * @ORM\Cache(usage="READ_WRITE", region="value_region")
 */
class ValueDate extends AbstractValue implements ValueTypeHasDefaultInterface
{
    /**
     * @var \DateTime|null
     * @ORM\Column( name="val", type="date", nullable=true )
     */
    protected $value;

    /**
     * @return string
     */
    public function getCastType(): string
    {
        return \DateTime::class;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $v = $this->value;
        if(!$v) {
            return '';
        }

        return $v->format('Y-m-d');
    }

}