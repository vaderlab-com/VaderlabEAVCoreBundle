<?php


namespace Vaderlab\EAV\Core\Service\Schema;


use ArrayAccess;
use Doctrine\Common\Collections\ArrayCollection;
use Vaderlab\EAV\Core\Model\SchemaInterface;

interface EAVSchemaManagerInterface
{
    public function createSchema(String $name, ArrayAccess $attributes): SchemaInterface;

    public function findById(int $id): ?SchemaInterface;

    public function findByClass(string $classname): ?SchemaInterface;

    public function findByName(string $name): ?SchemaInterface;

    public function findAll(): ArrayCollection;

    public function findAllProtectedSchemes(): ArrayCollection;
}