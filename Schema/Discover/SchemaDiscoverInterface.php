<?php


namespace Vaderlab\EAV\Core\Schema\Discover;


use Doctrine\Common\Collections\Collection;
use Vaderlab\EAV\Core\Model\SchemaInterface;

interface SchemaDiscoverInterface
{
    /**
     * @return Collection<SchemaInterface>
     */
    public function getSchemes(): Collection;

    /**
     * @param string $classname
     * @return SchemaInterface
     */
    public function getSchemaByClass(string $classname): SchemaInterface;
}