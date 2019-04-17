<?php


namespace Vaderlab\EAV\Core\Entity\ValueType;


use Doctrine\ORM\Mapping as ORM;

/**
 * Class ValueUrl
 * @package Vaderlab\EAV\Core\Entity\ValueType
 * @ORM\Entity()
 * @ORM\Cache(usage="READ_WRITE", region="value_region")
 */
class ValueUrl extends ValueString
{
    /**
     * @var float
     * @ORM\Column( name="val", type="string", nullable=true, length=2048)
     * @Assert\Url(
     *      message = "The value '{{ value }}' is not a valid URL.",
     *      protocols = {"http", "https", "ftp"},
     *      relativeProtocol = true
     * )
     */
    protected $value;
}