<?php

namespace App\Repository;

use App\Entity\GooglePlace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GooglePlace|null find($id, $lockMode = null, $lockVersion = null)
 * @method GooglePlace|null findOneBy(array $criteria, array $orderBy = null)
 * @method GooglePlace[]    findAll()
 * @method GooglePlace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository
 */
class GooglePlaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GooglePlace::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(GooglePlace $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(GooglePlace $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
