<?php

namespace App\Command\Load;

use App\Entity\Resource;
use App\Entity\ResourceType;
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

class ResourcesCommand extends Command
{
    protected static $defaultName = 'load:resources';
    protected static $defaultDescription = 'Load data to the resource table for downstream parsing';

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

    public function __construct(HttpClientInterface $client, EntityManagerInterface $manager, KernelInterface $kernel) {
        $this->manager = $manager;
        $this->client = $client;
        $this->scopleyVersion = $kernel->getContainer()->getParameter('scopley_version');
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null,InputOption::VALUE_OPTIONAL, 'Output the requests generated, do not pull data', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');

        $resourceRepo = $this->manager->getRepository(ResourceType::class);
        $resourceTypes = $resourceRepo->findAll();

        $queries = [];

        /** @var ResourceType $resourceType */
        foreach ($resourceTypes as $resourceType) {
            switch($resourceType->getUrlType()){
                case 1:
                    $queries[$resourceType->getId()] = 'https://api.stfc.dev/v1/' . $resourceType->getSlug() . '?version=' . $this->scopleyVersion . '&n=1208449541';
                    break;
                case 2:
                    $queries[$resourceType->getId()] = 'https://api.stfc.dev/v1/translations/en/' . $resourceType->getSlug() . '?version=' . $this->scopleyVersion . '&n=1208449541';
                    break;
            }
        }
        if(!$dryRun){
            foreach ($queries as $resourceTypeId => $query) {

                $response = $this->client->request('GET', $query);
                if($response->getStatusCode() !== 200){
                    $io->caution($query);
                } else {
                    $resource = new Resource();
                    $resourceType = $resourceRepo->find($resourceTypeId);
                    $resource
                        ->setName($resourceType->getName())
                        ->setType($resourceType)
                        ->setJson($response->toArray())
                        ->setRequestUrl($query)
                        ->setLastUpdated(new \DateTime());
                    $this->manager->persist($resource);
                    $this->manager->flush();
                }
            }
        } else {
            $io->text($queries);
        }

        $io->success('Completed');

        return Command::SUCCESS;
    }
}
