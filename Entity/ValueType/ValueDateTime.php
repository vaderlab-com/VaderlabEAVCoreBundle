<?php


namespace Vaderlab\EAV\Core\Entity\ValueType;
use Doctrine\ORM\Mapping as ORM;
use Vaderlab\EAV\Core\Entity\AbstractValue;

/**
 * Class ValueDateTime
 * @package Vaderlab\EAV\Core\Entity\ValueType
 * @ORM\Entity()
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="eav")
 */
class ValueDateTime extends AbstractValue
{
    /**
     * @var \DateTime|null
     * @ORM\Column( name="val", type="datetime", nullable=true )
     */
    protected $value;

    /**
     * @return string
     */
    public function __toString(): string
    {
        $v = $this->value;
        if(!$v) {
            return '';
        }

        return $v->format(DATE_ISO8601);
    }

    /**
     * @return string
     */
    public function getCastType(): string
    {
        return \DateTime::class;
    }
}