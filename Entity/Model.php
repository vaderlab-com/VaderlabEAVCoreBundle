<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 3.12.18
 * Time: 15.34
 */

namespace Vaderlab\Entity;


class Model
{

    private $attributes = [];

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return Attribute|null
     */
    public function getAttribute(): ?Attribute
    {
        return $this->attributes[0];
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function setAttributes( array $attributes ): Model
    {

        $this->attributes = $attributes;

        return $this;
    }
}