<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-13
 * Time: 01:37
 */

namespace Vaderlab\EAV\Core\Entity;


interface ValueTypeHasDefaultInterface extends ValueInterface
{
    /**
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * @param $value
     * @return ValueTypeHasDefaultInterface
     */
    public function setDefaultValue($value): ValueTypeHasDefaultInterface;
}