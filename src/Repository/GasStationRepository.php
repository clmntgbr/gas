<?php

namespace App\Repository;

use App\Entity\GasStation;
use App\Lists\GasStationStatusReference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GasStation|null find($id, $lockMode = null, $lockVersion = null)
 * @method GasStation|null findOneBy(array $criteria, array $orderBy = null)
 * @method GasStation[]    findAll()
 * @method GasStation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<GasStation>
 */
class GasStationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GasStation::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(GasStation $entity, bool $flush = true): void
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
    public function remove(GasStation $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return GasStation[]|null
     */
    public function findGasStationStatusNotClosed()
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->innerJoin('s.gasStationStatus', 'ss')
            ->where('ss.reference != :reference')
            ->setParameter('reference', GasStationStatusReference::CLOSED)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return mixed[]
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function findGasStationById()
    {
        $query = $this->createQueryBuilder('s')
            ->select('s.id')
            ->orderBy('s.id', 'ASC')
            ->indexBy('s', 's.id')
            ->getQuery();

        return $query->getResult();
    }


    /**
     * @return mixed[]
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function getGasStationsUpForDetails()
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->innerJoin('s.gasStationStatus', 'ss')
            ->where('ss.reference = :reference')
            ->setParameter('reference', GasStationStatusReference::FOUND_ON_GOV_MAP)
            ->setMaxResults(10)
            ->getQuery();

        return $query->getResult();
    }


    /**
     * @return mixed[]
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function getGasStationGooglePlaceByPlaceId(string $placeId)
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->innerJoin('s.googlePlace', 'ss')
            ->where('ss.placeId = :placeId')
            ->setParameter('placeId', $placeId)
            ->getQuery();

        return $query->getResult();
    }
}
