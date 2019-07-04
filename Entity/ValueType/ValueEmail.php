<?php


namespace Vaderlab\EAV\Core\Entity\ValueType;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vaderlab\EAV\Core\Entity\AbstractValue;

/**
 * Class ValueEmail
 * @package Vaderlab\EAV\Core\Entity\ValueType
 * @ORM\Entity()
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="eav")
 */
class ValueEmail extends AbstractValue
{
    /**
     * @var string
     * @ORM\Column( name="val", type="string", nullable=true)
     * @Assert\Email(
     *     message = "The value '{{ value }}' is not a valid email."
     * )
     */
    protected $value;

    /**
     * @return string
     */
    public function getCastType(): string
    {
        return 'string';
    }
}