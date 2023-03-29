<?php

namespace App\Repository;

use App\Entity\Flat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Flat>
 *
 * @method Flat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Flat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Flat[]    findAll()
 * @method Flat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Flat::class);
    }

    public function save(Flat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Flat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Flat[] Returns an array of Flat objects
     */
    public function flatSalad()
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.id IN (:val)')
            ->setParameter('val', [5, 15])
            ->getQuery()
            ->getResult()
        ;
    }
    

    /**
     * @return Flat[] Returns an array of Flat objects
     */
    public function flatFlat()
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.id IN (:val)')
            ->setParameter('val', [9, 13])
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @return Flat[] Returns an array of Flat objects
     */
    public function flatCheese()
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.id IN (:val)')
            ->setParameter('val', [8, 11])
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @return Flat[] Returns an array of Flat objects
     */
    public function flatDessert()
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.id IN (:val)')
            ->setParameter('val', [17, 18])
            ->getQuery()
            ->getResult()
        ;
    }



//    public function findOneBySomeField($value): ?Flat
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
