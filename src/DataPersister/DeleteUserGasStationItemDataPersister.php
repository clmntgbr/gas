<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use App\Entity\UserGasStation;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\Security;

class DeleteUserGasStationItemDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private Security               $security,
        private EntityManagerInterface $em
    )
    {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof UserGasStation;
    }

    public function persist($data, array $context = []): UserGasStation
    {
        return $data;
    }

    /**
     * @param UserGasStation $data
     */
    public function remove($data, array $context = [])
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if (null === $user) {
            throw new Exception('Missing user.');
        }

        if ($user->getId() !== $data->getUser()->getId()) {
            throw new Exception('You can\'t delete this item.');
        }

        $this->em->remove($data);
        $this->em->flush();

        return $data;
    }
}