<?php

namespace App\Command\Load;

use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class LoaderCommand extends Command {

    /**
     * @var EntityManager
     */
    protected $manager;
    /**
     * @var HttpClient
     */
    protected $client;
    /**
     * @var string
     */
    protected $scopleyVersion;
    /**
     * @var SymfonyStyle
     */
    protected $io;
    /**
     * @var boolean
     */
    protected $dryRun;
    /**
     * @var boolean
     */
    protected $seed;


    public function __construct(HttpClientInterface $client, EntityManagerInterface $manager) {
        $this->manager = $manager;
        $this->client = $client;
        parent::__construct();
    }

    protected function configure(): void {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Output the requests generated, do not pull data')
            ->addOption('seed', null, InputOption::VALUE_NONE, 'Truncate the requests table and force reload, no updates');
    }

    /**
     * Truncate the TableName passed.  If self::dryRun === true, it will begin a transaction and delete the TableName
     * contents.
     *
     * @param string $tableName
     *
     * @return int|null
     */
    protected function clearTable(string $tableName): ?int {
        if ($this->dryRun) {
            /*
             * because TRUNCATE is a DDL function and NOT a DML function, it can not be contained inside a
             * transaction for a dryRun type feature
             */
            $this->manager->beginTransaction();
            $sql = "DELETE FROM ${tableName};";
        } else {
            $sql = "TRUNCATE TABLE ${tableName};";
        }
        try {
            $stmt = $this->manager->getConnection()->prepare($sql);
            $stmt->executeQuery();
            return null;
        } catch (\Doctrine\DBAL\Exception | Exception $e) {
            $this->io->error('Unable to TRUNCATE database table');
            return self::FAILURE;
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->io = new SymfonyStyle($input, $output);
        $this->dryRun = $input->getOption('dry-run');
        $this->seed = $input->getOption('seed');
        return Command::SUCCESS;
    }
}
