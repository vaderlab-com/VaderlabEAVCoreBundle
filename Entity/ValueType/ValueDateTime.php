<?php


namespace Vaderlab\EAV\Core\Entity\ValueType;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ValueDateTime
 * @package Vaderlab\EAV\Core\Entity\ValueType
 * @ORM\Entity()
 * @ORM\Cache(usage="READ_WRITE", region="value_region")
 */
class ValueDateTime extends ValueDate
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
}