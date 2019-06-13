<?php


namespace Vaderlab\EAV\Core\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vaderlab\EAV\Core\Schema\Diff\Diff;
use Vaderlab\EAV\Core\Schema\Discover\SchemaDiscoverInterface;

class SchemaUpdateCommand extends Command
{
    /**
     * @var ProtectedSchemasDiscovery
     */
    private $diff;

    public function __construct(Diff $diff) {
        parent::__construct();

        $this->diff = $diff;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Update EAV schema')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Synchronization of database schema based on models in code');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$classes = $this->schemesDiscoveryORM->getSchema();
        $diff = $this->diff->diff();

        dump($diff);
    }
}