<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-06
 * Time: 16:48
 */

namespace Vaderlab\EAV\Core\Entity\ValueType;

use Vaderlab\EAV\Core\Entity\AbstractValue;
use Doctrine\ORM\Mapping as ORM;
use Vaderlab\EAV\Core\Entity\ValueTypeHasDefaultInterface;

/**
 * Class ValueString
 * @package Vaderlab\EAV\Core\Entity\ValueType
 * @ORM\Table(name="vaderlab_eav_value_string")
 * @ORM\Entity()
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="eav")
 */
class ValueString extends AbstractValue implements ValueTypeHasDefaultInterface
{
    /**
     * @var string
     * @ORM\Column( name="val", type="string", length=2048, nullable=true )
     */
    protected $value = '';

    /**
     * @return string
     */
    public function getCastType(): string
    {
        return 'string';
    }
}