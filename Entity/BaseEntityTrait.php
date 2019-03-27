<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-14
 * Time: 00:45
 */

namespace Vaderlab\EAV\Core\Entity;


use Doctrine\ORM\Mapping as ORM;


trait BaseEntityTrait
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}