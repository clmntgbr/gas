<?php

namespace App\Repository;

use App\Entity\GasStationStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GasStationStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method GasStationStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method GasStationStatus[]    findAll()
 * @method GasStationStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository
 */
class GasStationStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GasStationStatus::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(GasStationStatus $entity, bool $flush = true): void
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
    public function remove(GasStationStatus $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
