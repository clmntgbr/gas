<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use App\Entity\UserGasStation;
use App\Repository\UserGasStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\Security;

class PostUserGasStationCollectionDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private Security                 $security,
        private UserGasStationRepository $userGasStationRepository,
        private EntityManagerInterface   $em
    )
    {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof UserGasStation;
    }

    /**
     * @param UserGasStation $data
     */
    public function persist($data, array $context = []): UserGasStation
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if (null === $user) {
            throw new Exception('Missing user.');
        }

        $userGasStation = $this->userGasStationRepository->findOneBy([
            'user' => $user,
            'gasStation' => $data->getGasStation(),
        ]);

        if ($userGasStation instanceof UserGasStation) {
            return $userGasStation;
        }

        $data->setUser($user);
        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }

    public function remove($data, array $context = [])
    {
        return $data;
    }
}