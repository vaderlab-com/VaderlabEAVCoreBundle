<?php


namespace Vaderlab\EAV\Core\Annotation;


use Doctrine\ORM\Mapping\Annotation;

/**
 * Class Entity
 * @package Vaderlab\EAV\Core\Annotation
 *
 * @Annotation
 * @Target({"ANNOTATION", "PROPERTY"})
 */
class Id  extends BaseAttribute
{
    public $target;

    public function __toString(): string
    {
        return 'id';
    }
}