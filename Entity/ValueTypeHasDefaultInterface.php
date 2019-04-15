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
     * @return string
     */
    public function getCastType(): string;
}