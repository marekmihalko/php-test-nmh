<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use Predis\Client;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class CacheService
{
    /**
     * @var Client
     */
    private $redisDefault;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var RepositoryManagerInterface
     */
    private $repositoryManager;

    public function __construct(Client $redisDefault, EntityManagerInterface $entityManager, ContainerInterface $container, RepositoryManagerInterface $repositoryManager)
    {
        $this->redisDefault = $redisDefault;
        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->repositoryManager = $repositoryManager;
    }

    public function removeCacheEntityById($id, string $cacheSuffix = 'product')
    {
        $this->redisDefault->del("{$id}_{$cacheSuffix}");
    }

    public function cacheEntityById($id, string $className, array $groupsArray = ['product'], string $cacheSuffix = 'product')
    {
        $item = $this->redisDefault->get("{$id}_{$cacheSuffix}");
        if ($item) {
            return json_decode($item, true);
        }

        $entity = $this->entityManager->getRepository($className)->find($id);

        if (!$entity) {
            return null;
        }

        $serializedEntityJson = $this->container->get('serializer')->serialize($entity, 'json',
            [
                'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
                'groups' => $groupsArray
            ]);

        $this->redisDefault->set("{$id}_{$cacheSuffix}", $serializedEntityJson);

        return json_decode($serializedEntityJson, true);
    }

    public function getCachedListingData($search, $page, $limitPerPage, string $className, array $groupsArray = ['product'], string $cacheSuffix = 'product')
    {
        $item = $this->redisDefault->get("{$search}_{$page}_{$limitPerPage}_{$cacheSuffix}");
        if ($item) {
            return json_decode($item, true);
        }

        $repository = $this->repositoryManager->getRepository($className);
        $itemRepository = $repository->findPaginated($search)
            ->setCurrentPage($page)
            ->setMaxPerPage($limitPerPage);

        $serializedEntityListJson = $this->container->get('serializer')->serialize($itemRepository->getCurrentPageResults(), 'json',
            [
                'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
                'groups' => $groupsArray
            ]);

        $listingDataArray = [
            'totalCount' => $itemRepository->count(),
            'page' => $page,
            'limitPerPage' => $limitPerPage,
            'items' => json_decode($serializedEntityListJson, true)
        ];

        $this->redisDefault->set("{$search}_{$page}_{$limitPerPage}_{$cacheSuffix}", json_encode($listingDataArray), 'EX', 45);

        return $listingDataArray;
    }
}