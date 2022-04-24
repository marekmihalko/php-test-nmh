<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
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

    public function __construct(Client $redisDefault, EntityManagerInterface $entityManager, ContainerInterface $container)
    {
        $this->redisDefault = $redisDefault;
        $this->entityManager = $entityManager;
        $this->container = $container;
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

}