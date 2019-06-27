<?php


namespace Vaderlab\EAV\Core\Schema\Discover\ORM;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Vaderlab\EAV\Core\Entity\Schema;
use Vaderlab\EAV\Core\Model\SchemaInterface;
use Vaderlab\EAV\Core\Schema\Discover\SchemaDiscoverInterface;
use Vaderlab\EAV\Core\Service\Schema\EAVSchemaManager;
use Vaderlab\EAV\Core\Service\Schema\EAVSchemaManagerInterface;

/**
 * Class SchemaDiscover
 * @package Vaderlab\EAV\Core\Schema\Discover\ORM
 */
class SchemaDiscover implements SchemaDiscoverInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $schemaManager;

    /**
     * SchemaDiscover constructor.
     * @param EAVSchemaManagerInterface $schemaManager
     */
    public function __construct(EAVSchemaManagerInterface $schemaManager)
    {
        $this->schemaManager = $schemaManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getSchemes(): Collection
    {
        return $this->schemaManager->findAllProtectedSchemes();
    }

    /**
     * @param string $classname
     * @return SchemaInterface
     */
    public function getSchemaByClass(string $classname): SchemaInterface
    {
        return $this->schemaManager->findByClass($classname);
    }
}