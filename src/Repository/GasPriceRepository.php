<?php

namespace App\Repository;

use App\Entity\GasPrice;
use App\Entity\GasStation;
use App\Entity\GasType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method GasPrice|null find($id, $lockMode = null, $lockVersion = null)
 * @method GasPrice|null findOneBy(array $criteria, array $orderBy = null)
 * @method GasPrice[]    findAll()
 * @method GasPrice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository
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

    /**
     * @throws QueryException
     * @throws NonUniqueResultException
     */
    public function findLastGasPriceByTypeAndGasStationExceptId(GasStation $gasStation, GasType $gasType, int $gasPriceId): ?GasPrice
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

    /**
     * @throws QueryException
     * @throws NonUniqueResultException
     */
    public function findLastGasPriceByTypeAndGasStation(GasStation $gasStation, GasType $gasType): ?GasPrice
    {
        return $this->createQueryBuilder('g')
            ->where('g.gasStation = :gs')
            ->andWhere('g.gasType = :gt')
            ->setParameters([
                'gs' => $gasStation,
                'gt' => $gasType,
            ])
            ->orderBy('g.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * @return array<mixed>
     * @throws QueryException
     */
    public function getGasPriceCountByGasStation(GasStation $gasStation)
    {
        $query = "  SELECT count(*) as gas_price_count
                    FROM gas_price p
                    WHERE p.gas_station_id = %s;";

        $query = sprintf($query, $gasStation->getId());

        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $result = $statement->executeQuery()->fetchAssociative();
        if (false === $result) {
            throw new Exception('Sql issue');
        }

        return $result;
    }


    /**
     * @throws QueryException
     */
    public function findLastGasPriceByGasStation(GasStation $gasStation): ?GasPrice
    {
        return $this->createQueryBuilder('g')
            ->where('g.gasStation = :gs')
            ->setParameters([
                'gs' => $gasStation,
            ])
            ->orderBy('g.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
