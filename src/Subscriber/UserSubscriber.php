<?php

namespace App\Subscriber;

use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserSubscriber implements EventSubscriber
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private ValidatorInterface          $validator
    )
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $user = $args->getObject();

        if (!$user instanceof User) {
            return;
        }

        if (null === $user->getPlainPassword()) {
            throw new \Exception('PlainPassword is missing.');
        }

        $errors = $this->validator->validate($user, null, ['user.post']);

        if (count($errors) > 0) {
            throw new \Exception(sprintf('%s errors : %s', get_class($user), $errors));
        }

        $user
            ->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPlainPassword()))
            ->setIsEnable(true)
            ->eraseCredentials();
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $user = $args->getObject();

        if (!$user instanceof User) {
            return;
        }

        if (null !== $user->getPlainPassword()) {
            $user
                ->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPlainPassword()))
                ->eraseCredentials();
        }
    }
}