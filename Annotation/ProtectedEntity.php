<?php


namespace Vaderlab\EAV\Core\Annotation;

use Doctrine\ORM\Mapping\Annotation;

/**
 * Class Entity
 * @package Vaderlab\EAV\Core\Annotation
 *
 * @Annotation
 * @Target({"CLASS"})
 */
class ProtectedEntity implements Annotation
{
    /**
     * @var null|string
     */
    public $name;

    /**
     * Entity constructor.
     * @param $config
     */
/*    public function __construct($annotations)
    {
        $config = $annotations['value'];

        foreach ($config as $k => $v) {
            $this->{$k} = $v;
        }
    }
*/
    public function __toString(): string
    {
        return $this->name;
    }
}