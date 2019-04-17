<?php


namespace Vaderlab\EAV\Core\Entity\ValueType;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ValueEmail
 * @package Vaderlab\EAV\Core\Entity\ValueType
 * @ORM\Entity()
 * @ORM\Cache(usage="READ_WRITE", region="value_region")
 */
class ValueEmail extends ValueString
{
    /**
     * @var float
     * @ORM\Column( name="val", type="string", nullable=true)
     * @Assert\Email(
     *     message = "The value '{{ value }}' is not a valid email."
     * )
     */
    protected $value;
}