<?php
/**
 * Created by PhpStorm.
 * User: Mi
 * Date: 29.03.2019
 * Time: 21:29
 */

namespace Vaderlab\EAV\Core\Entity;


use Doctrine\Common\Collections\Collection;

interface EAVEntityInterface
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return Schema
     */
    public function getSchema(): ?Schema;

    /**
     * @return ValueInterface
     */
    public function getValues(): Collection;
}