<?php


namespace Vaderlab\EAV\Core\Schema\Diff;


use Vaderlab\EAV\Core\Schema\Discover\SchemaDiscoverInterface;

class Diff
{
    /**
     * @var SchemaDiscoverInterface
     */
    private $dbDiscover;

    /**
     * @var SchemaDiscoverInterface
     */
    private $classDiscover;

    /**
     * Diff constructor.
     * @param SchemaDiscoverInterface $dbDiscover
     * @param SchemaDiscoverInterface $classDiscover
     */
    public function __construct(
        SchemaDiscoverInterface $dbDiscover,
        SchemaDiscoverInterface $classDiscover
    )
    {
        $this->dbDiscover = $dbDiscover;
        $this->classDiscover = $classDiscover;
    }

    public function diff()
    {
        $first = $this->dbDiscover->getSchema();
        $second = $this->classDiscover->getSchema();


    }
}