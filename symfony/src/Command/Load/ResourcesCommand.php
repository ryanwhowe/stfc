<?php

namespace App\Command\Load;

use App\Entity\Resource;
use App\Entity\ResourceType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ResourcesCommand extends LoaderCommand {

    const QUERY_URL = 'url';
    const QUERY_RESOURCE = 'resource';
    const QUERY_TYPE = 'type';

    const QUERY_TYPE_UPDATE = 'update';
    const QUERY_TYPE_INSERT = 'insert';

    protected static $defaultName = 'load:resources';
    protected static $defaultDescription = 'Load data to the resource table for downstream parsing';

    public function __construct(HttpClientInterface $client, EntityManagerInterface $manager, KernelInterface $kernel) {
        $this->scopleyVersion = $kernel->getContainer()->getParameter('scopley_version');
        parent::__construct($client, $manager);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        parent::execute($input, $output);

        if ($this->seed) {
            $results = $this->clearTable( 'resource');
            if ($results !== null) return $results;
        }

        $resourceTypes = $this->manager->getRepository(ResourceType::class)->findAll();

        $counter = [self::QUERY_TYPE_INSERT => 0, self::QUERY_TYPE_UPDATE => 0];
        $queries = [];

        /** @var ResourceType $resourceType */
        foreach ($resourceTypes as $resourceType) {

            // check for existing resources to update
            if (count($resourceType->getResources()) === 0) {
                switch ($resourceType->getUrlType()) {
                    case 1:
                        $queries[] =
                            [
                                self::QUERY_TYPE     => self::QUERY_TYPE_INSERT,
                                self::QUERY_URL      => 'https://api.stfc.dev/v1/' . $resourceType->getSlug() . '?version=' . $this->scopleyVersion . '&n=1208449541',
                                self::QUERY_RESOURCE => $resourceType,
                            ];
                        break;
                    case 2:
                        $queries[] =
                            [
                                self::QUERY_TYPE     => self::QUERY_TYPE_INSERT,
                                self::QUERY_URL      => 'https://api.stfc.dev/v1/translations/en/' . $resourceType->getSlug() . '?version=' . $this->scopleyVersion . '&n=1208449541',
                                self::QUERY_RESOURCE => $resourceType,
                            ];
                        break;
                }
            } else {
                /** @var Resource $resource */
                foreach ($resourceType->getResources() as $resource) {
                    $queries[] = [
                        self::QUERY_TYPE     => self::QUERY_TYPE_UPDATE,
                        self::QUERY_URL      => $resource->getRequestUrl(),
                        self::QUERY_RESOURCE => $resource,
                    ];
                }
            }
        }
        if (!$this->dryRun) {
            foreach ($queries as $query) {
                try {
                    $response = $this->client->request('GET', $query[self::QUERY_URL]);
                    $status = $response->getStatusCode();
                } catch (TransportExceptionInterface $e) {
                    $this->io->error('Transportation Exception for: ' . $query[self::QUERY_URL]);
                    continue;
                }
                if ($status !== 200) {
                    $this->io->error("URL request did not resolve:\n" . $query[self::QUERY_URL]);
                    continue;
                }
                try { //make sure we get a valid array returned from the body parser
                    $body = $response->toArray();
                } catch (ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
                    $this->io->error('Response body could not be parsed');
                    continue;
                }
                if ($query[self::QUERY_TYPE] === self::QUERY_TYPE_INSERT) {
                    $resource = new Resource();
                    $resourceType = $query[self::QUERY_RESOURCE];
                    $resource
                        ->setName($resourceType->getName())
                        ->setType($resourceType)
                        ->setJson($body)
                        ->setRequestUrl($query[self::QUERY_URL])
                        ->setLastUpdated(new DateTime());
                    $counter[self::QUERY_TYPE_INSERT]++;
                } else {
                    $resource = $query[self::QUERY_RESOURCE];
                    $resource
                        ->setJson($body)
                        ->setLastUpdated(new DateTime());
                    $counter[self::QUERY_TYPE_UPDATE]++;
                }
                try {
                    $this->manager->persist($resource);
                    $this->manager->flush();
                } catch (OptimisticLockException | ORMException $e) {
                    $this->io->error('Unable to persist resource for query: ' . $query[self::QUERY_URL]);
                }

            }

            $this->io->table(['Inserts', 'Updates'], [[$counter[self::QUERY_TYPE_INSERT], $counter[self::QUERY_TYPE_UPDATE]]]);

        } else {
            if ($this->seed) $this->manager->rollback();

            $queries = array_map(function ($v) {
                $type = ($v[self::QUERY_TYPE] === self::QUERY_TYPE_INSERT) ? 'INSERT' : 'UPDATE';
                return $type . ' : ' . $v[self::QUERY_URL];
            }, $queries);
            $this->io->text($queries);
        }

        $this->io->success('Completed');

        return Command::SUCCESS;
    }
}
