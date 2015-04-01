<?php

namespace Avid\CandidateChallenge\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Kevin Archer <kevin.archer@avidlifemedia.com>
 */
final class DumpTableStructure extends Command
{
    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        parent::__construct();
    }

    /**
     * Command configurations
     */
    protected function configure()
    {
        $this->setName('dump:tables')->setDescription('Dump the current database table structures to a directory')
            ->addOption('directory', 'd', InputOption::VALUE_REQUIRED, 'Where to dump the sql', __DIR__ . '/../../resources/sql')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Display tables only');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Connection $connection
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output, Connection $connection = NULL)
    {
        $output->writeln('');

        $output->write('<info>Dumping the table structures</info>');

        // Notify user if it's a dry-run
        if ($input->getOption('dry-run')) {
            $output->write(' <comment>(dry run only)</comment>');
        }

        $output->writeln('');

        /** @var AbstractSchemaManager $sm */
        $sm = $this->connection->getSchemaManager();

        $output->writeln("Platform: <comment>{$sm->getDatabasePlatform()->getName()}</comment>");

        // List all tables and render structure inforamtion output
        $tbls = $sm->listTables();

        /** @var TableHelper $ot */
        $ot = $this->getHelper('table');
        $ot->setHeaders(['tables']);

        $ot->addRows(
            array_map(function (Table $table) {
                return [$table->getName()];
                },
            $tbls)
        );

        $ot->render($output);

        $output->writeln('');

        foreach ($tbls as $tbl) {
            $output->writeln("<info>{$tbl->getName()}</info>");

            /** @var TableHelper $outputTable */
            $ot = $this->getHelper('table');
            $ot->setHeaders([
                    'column',
                      'type',
                     'length',
                      'not null']
            );

            foreach ($tbl->getColumns() as $c) {
                $ot->addRow(
                    [$c->getName(), $c->getType(), $c->getLength(), $c->getNotnull()]
                );
            }

            $ot->render($output);
            $output->writeln('');

            $data = "";
            foreach ($sm->getDatabasePlatform()->getCreateTableSQL($tbl) as $stmt) {
                $data .= $stmt . ';';
            }

            $path = $input->getOption('directory') . '/' . $tbl->getName() . '.sql';
            if ($realPath = realpath($path)) {
                $path = $realPath;
            }

            // Save create table sql to specified directory if it's not a dry-run
            if ($input->getOption('dry-run') == FALSE) {
                $output->writeln("<info>Writing $path</info>");
                file_put_contents(
                    $path,
                    \SqlFormatter::format($data, false)
                );
            } else {
                $output->writeln("<info>Table structure will be saved to: $path</info>");
                $output->writeln($data);
            }
        }

        $output->writeln('');
    }
}
