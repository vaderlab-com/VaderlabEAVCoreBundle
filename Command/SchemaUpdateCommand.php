<?php


namespace Vaderlab\EAV\Core\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vaderlab\EAV\Core\Schema\Diff\Diff;

class SchemaUpdateCommand extends Command
{

    const OPTION_SCHEMA_UPDATE          = 'force';
    const OPTION_SCHEMA_UPDATE_SHORT    = 'f';
    const OPTION_DUMP                   = 'dump';
    const OPTION_DUMP_SHORT             = 'd';


    /**
     * @var Diff
     */
    private $diffService;

    /**
     * SchemaUpdateCommand constructor.
     * @param Diff $diffService
     */
    public function __construct(
        Diff $diffService
    ) {
        parent::__construct();

        $this->diffService = $diffService;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Update EAV schema')
            ->addOption(
                self::OPTION_SCHEMA_UPDATE,
                'f',
                InputOption::VALUE_NONE,
                ''
            )
            ->addOption(
                self::OPTION_DUMP,
                self::OPTION_DUMP_SHORT,
                InputOption::VALUE_OPTIONAL,
                '',
                true
            )
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Synchronization of database schema based on models in code');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $isDump     = $input->getOption(self::OPTION_DUMP);
        $isUpdate   = $input->getOption(self::OPTION_SCHEMA_UPDATE);

        $diffArray  = $this->createDiff($isUpdate);

        if(!$isDump) {
            return;
        }

        $this->showDiff($output, $diffArray);

        if(!$isUpdate) {
            $output->writeln('<question>To perform the migration, run the command using the "--force" flag</question>');
        }
    }

    /**
     * @param bool $isUpdate
     * @return array
     */
    protected function createDiff(bool $isUpdate)
    {
        return $this->diffService->diff($isUpdate);
    }

    /**
     * @param OutputInterface $output
     * @param array $diff
     *
     * TODO: Temporaty solution
     */
    protected function showDiff(OutputInterface $output, array $diff): void
    {
        if(!count($diff)) {
            $output->writeln('<bg=green;options=bold>No new changes</>');

            return;
        }

        $output->writeln('<bg=green;options=bold>DIFF CHANGES</>');

        foreach ($diff as $classname => $attributesDiff) {
            $output->writeln(sprintf('<fg=black;bg=cyan>%s</>', $classname));
            foreach ($attributesDiff['attributes'] as $attribute => $attrData) {
                $isNew = $attrData['is_new'];
                $output->writeln(sprintf('<options=bold>    %s</> (%s)', $attribute,
                    ($isNew ? 'Append': 'Update')
                ));
                unset($attrData['is_new']);

                foreach ($attrData as $propName => $propDiff) {
                    $valNew = var_export($propDiff['new'], 1);
                    if($isNew) {
                        $output->writeln(
                            sprintf('<options=bold>        %s: </> %s',
                                $propName, $valNew)
                        );

                        continue;
                    }

                    $valOld = var_export($propDiff['old'], 1);

                    $output->writeln(
                        sprintf('<options=bold>        %s: </> %s => %s',
                            $propName, $valOld, $valNew)
                    );
                }

            }

        }

    }
}