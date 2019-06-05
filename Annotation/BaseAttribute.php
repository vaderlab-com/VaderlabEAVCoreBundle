<?php


namespace Vaderlab\EAV\Core\Annotation;


use Doctrine\ORM\Mapping\Annotation;

abstract class BaseAttribute implements Annotation
{
    public $target;
}