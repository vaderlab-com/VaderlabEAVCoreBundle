<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 3.12.18
 * Time: 15.36
 */

namespace Vaderlab\Entity;


class Attribute
{
    /**
     * @var String
     */
    private $name;

    /**
     * @var String
     */
    private $type;

    /**
     * @var Model
     */
    private $model;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }


    public function setName( string $name ): ?string
    {

        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return Model
     */
    public function getModel(): ?Model
    {
        return $this->model;
    }

    /**
     * @param Model $model
     * @return Attribute
     */
    public function setModel( Model $model ): Attribute
    {
        $this->model = $model;

        return $this;
    }
}