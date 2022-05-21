<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;
use App\Entity\UserGasStation;
use App\Repository\UserGasStationRepository;
use Symfony\Component\Security\Core\Security;

class GetUserGasStationCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(
        private Security                 $security,
        private UserGasStationRepository $gasStationRepository
    )
    {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return UserGasStation::class === $resourceClass && $operationName === 'get';
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if (null === $user) {
            return [];
        }

        return $this->gasStationRepository->findBy(['user' => $user]);
    }
}