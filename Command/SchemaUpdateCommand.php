<?php


namespace Vaderlab\EAV\Core\Command;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vaderlab\EAV\Core\Model\AttributeInterface;
use Vaderlab\EAV\Core\Model\SchemaInterface;
use Vaderlab\EAV\Core\Schema\Diff\Comparison\AttributeCompareProcessor;
use Vaderlab\EAV\Core\Schema\Diff\Comparison\SchemaCompareProcessor;
use Vaderlab\EAV\Core\Schema\Discover\SchemaDiscoverInterface;

class SchemaUpdateCommand extends Command
{
    private $dbDiscover;

    private $fsDiscover;

    private $processor;

    private $processor2;

    private $entityManager;

    public function __construct(
        SchemaDiscoverInterface $dbDiscover ,
        SchemaDiscoverInterface $fsDiscover,
        AttributeCompareProcessor $processor,
        SchemaCompareProcessor $processor2,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();

        $this->dbDiscover = $dbDiscover;
        $this->fsDiscover = $fsDiscover;
        $this->processor = $processor;
        $this->processor2 = $processor2;
        $this->entityManager = $entityManager;
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

        $callable = function (SchemaInterface $sch) {
            return $sch->getEntityClass() === 'App\Entity\User';
        };

        $fsSch = $this->fsDiscover->getSchemes()->filter($callable)->first();
        $dbSch = $this->dbDiscover->getSchemes()->filter($callable)->first();

        //dump($this->fsDiscover->getSchemes());
        //exit;


        /*
        $callable = function (AttributeInterface $attr){
            return $attr->getName() === 'birthday';
        };

        $fsAttr = $fsSch->getAttributes()->filter($callable)->first();
        $dbAttr  = $dbSch->getAttributes()->filter($callable)->first();
        */

        //$diff = $this->processor->process($dbAttr, $fsAttr);

        $diff = $this->processor2->process($dbSch, $fsSch, true);

        $this->entityManager->persist($dbSch);
        $this->entityManager->flush();

        dump($diff);


        //dump($this->fsDiscover->getSchemes());


        //dump($diff);
    }
}