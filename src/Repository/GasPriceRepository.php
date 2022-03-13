<?php

namespace App\Repository;

use App\Entity\GasPrice;
use App\Entity\GasStation;
use App\Entity\GasType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GasPrice|null find($id, $lockMode = null, $lockVersion = null)
 * @method GasPrice|null findOneBy(array $criteria, array $orderBy = null)
 * @method GasPrice[]    findAll()
 * @method GasPrice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GasPriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GasPrice::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(GasPrice $entity, bool $flush = true): void
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
    public function remove(GasPrice $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findLastGasPriceByTypeAndGasStationExceptId(GasStation $gasStation, GasType $gasType, int $gasPriceId)
    {
        return $this->createQueryBuilder('g')
            ->where('g.gasStation = :gs')
            ->andWhere('g.gasType = :gt')
            ->andWhere('g.id != :g')
            ->setParameters([
                'gs' => $gasStation,
                'gt' => $gasType,
                'g' => $gasPriceId
            ])
            ->orderBy('g.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
