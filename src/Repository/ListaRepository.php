<?php

namespace App\Repository;

use App\Entity\Lista;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Lista|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lista|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lista[]    findAll()
 * @method Lista[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lista::class);
    }

    // /**
    //  * @return Lista[] Returns an array of Lista objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Lista
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
