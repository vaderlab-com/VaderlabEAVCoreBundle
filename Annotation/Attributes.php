<?php


namespace Vaderlab\EAV\Core\Annotation;


use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\ORM\Mapping\Annotation;

/**
 * Class AttributesContainer
 * @package Vaderlab\EAV\Core\Annotation
 *
 * @Annotation
 * @Target({"CLASS"})
 */
class Attributes implements Annotation
{
    public $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes['value'];
    }
}