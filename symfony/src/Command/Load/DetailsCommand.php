<?php

namespace App\Command\Load;

use App\Entity\ResourceDetail;
use App\Entity\ResourceType;
use App\Stfc\Utils\StringUtils;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\Mapping\MappingException;
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

class DetailsCommand extends LoaderCommand {
    protected static $defaultName = 'load:details';
    protected static $defaultDescription = 'Load details data for a given resource or all resources';

    public function __construct(HttpClientInterface $client, EntityManagerInterface $manager, KernelInterface $kernel) {
        $this->scopleyVersion = $kernel->getContainer()->getParameter('scopley_version');
        parent::__construct($client, $manager);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        parent::execute($input, $output);

        $resourceTypes = $this->manager->getRepository(ResourceType::class)->findBy(['name' => 'System']);
        $ids = [];
        /** @var ResourceType $resourceType */
        foreach ($resourceTypes as $resourceType) {
            /** @var \App\Entity\Resource $resource */
            foreach ($resourceType->getResources() as $resource) {
                $resourceIds = StringUtils::reduceTopLevelIds($resource->getJson());
                $ids[$resourceType->getId()] = $resourceIds;
            }
        }

        $id_count = array_reduce($ids, function ($count, $v) {
            $count += count($v);
            return $count;
        }, 0);

        $this->io->progressStart($id_count);

        foreach ($ids as $resourceTypeId => $id_array) {
            $resourceType = $this->manager->getRepository(ResourceType::class)->find($resourceTypeId);
            foreach ($id_array as $id => $count) {
                ///https://api.stfc.dev/v1/system/2038739638?version=187df9eba69b701c9e203d851c052f91dd80eee29364b8cedaf58f28834cf7c9&n=1208449541
                ///https://api.stfc.dev/v1/system/917793981?version=187df9eba69b701c9e203d851c052f91dd80eee29364b8cedaf58f28834cf7c9&n=1208449541
                $url = 'https://api.stfc.dev/v1/' . $resourceType->getSlug() . '/' . $id . '?version=187df9eba69b701c9e203d851c052f91dd80eee29364b8cedaf58f28834cf7c9&n=1208449541';

                $response = $this->client->request('GET', $url);

                try {
                    $body = $response->toArray();
                } catch (ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
                    $this->io->error($url);
                    $this->io->progressAdvance();
                    usleep(500000);
                    continue;
                }
                $detail = new ResourceDetail();
                $detail
                    ->setScopleyId($id)
                    ->setTypeId($resourceType)
                    ->setRequestUrl($url)
                    ->setJson($body)
                    ->setLastUpdated(new DateTime)
                    ->setCreated(new DateTime);
                $this->io->progressAdvance();
                try {
                    $this->manager->persist($detail);
                    $this->manager->flush();
                    $this->manager->clear();
                } catch (ORMException | OptimisticLockException | MappingException $e) {
                    $this->io->error('Unable to persist resource detail for query: ' . $url);
                }
                sleep(1); /// stop from flooding the API, don't want to get shutdown before I get started
            }
        }

        $this->io->progressFinish();

        $this->io->success("Complete");

        return Command::SUCCESS;
    }
}
