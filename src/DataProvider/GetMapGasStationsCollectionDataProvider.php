<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Api\Controller\GetMapGasStations;
use App\Entity\GasStation;

class GetMapGasStationsCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return GasStation::class === $resourceClass && $operationName === GetMapGasStations::$operationName;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        if (array_key_exists('filters', $context)) {
            return $context['filters'];
        }

        throw new \Exception('Missing filters params.');
    }
}