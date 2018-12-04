<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 3.12.18
 * Time: 15.34
 */

namespace Vaderlab\EAV\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Vaderlab\EAV\Repository\ModelRepository")
 */
class Model
{

    private $attributes = [];

    private $createdAt;

    private $updatedAt;

    /**
     * @throws \Exception
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
    }

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