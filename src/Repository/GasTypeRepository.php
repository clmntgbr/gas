<?php

namespace App\Repository;

use App\Entity\GasType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GasType|null find($id, $lockMode = null, $lockVersion = null)
 * @method GasType|null findOneBy(array $criteria, array $orderBy = null)
 * @method GasType[]    findAll()
 * @method GasType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository
 */
class GasTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GasType::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(GasType $entity, bool $flush = true): void
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
    public function remove(GasType $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return mixed[]
     * @throws QueryException
     */
    public function findGasTypeById(): array
    {
        $query = $this->createQueryBuilder('t')
            ->select('t.id, t.reference, t.label')
            ->orderBy('t.id', 'ASC')
            ->indexBy('t', 't.id')
            ->getQuery();

        return $query->getResult();
    }
}
