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
     * @return GasStation[]
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

    public function getGasStationsForMap(string $longitude, string $latitude, string $radius)
    {
        $query = "  SELECT s.id as gas_station_id, m.path as preview_path, m.name as preview_name, s.address_id, s.company, 
                    s.last_gas_prices as gas_types, 
                    s.name as gas_station_name, s.last_gas_prices, s.previous_gas_prices, s.gas_station_status_id, s.google_place_id, a.vicinity,  a.longitude,  a.latitude,
                    p.url,
                    (SQRT(POW(69.1 * (a.latitude - $latitude), 2) + POW(69.1 * ($longitude - a.longitude) * COS(a.latitude / 57.3), 2))*1000) as distance,
                    (SELECT GROUP_CONCAT(gs.label SEPARATOR ', ')
                    FROM gas_stations_services gss
                    INNER JOIN gas_service gs ON gss.gas_service_id = gs.id
                    AND gss.gas_station_id = s.id) as gas_services
                    FROM gas_station s
                    INNER JOIN address a ON s.address_id = a.id
                    INNER JOIN media m ON s.preview_id = m.id
                    INNER JOIN gas_station_status gs ON s.gas_station_status_id = gs.id
                    LEFT JOIN google_place p ON p.id = s.google_place_id
                    WHERE a.longitude IS NOT NULL AND a.latitude IS NOT NULL AND gs.reference != 'closed'
                    HAVING `distance` < $radius
                    ORDER BY `distance` ASC LIMIT 300;
        ";

        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        return $statement->executeQuery()->fetchAllAssociative();
    }
}
