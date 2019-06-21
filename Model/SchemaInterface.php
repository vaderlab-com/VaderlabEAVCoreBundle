<?php


namespace Vaderlab\EAV\Core\Model;


use Doctrine\Common\Collections\Collection;

interface SchemaInterface
{
    public function getName(): ?string;

    public function getEntityClass(): ?string;

    public function getAttributes(): Collection;
}