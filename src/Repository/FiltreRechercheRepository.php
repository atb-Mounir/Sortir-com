<?php

namespace App\Repository;

use App\Entity\FiltreRecherche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FiltreRecherche|null find($id, $lockMode = null, $lockVersion = null)
 * @method FiltreRecherche|null findOneBy(array $criteria, array $orderBy = null)
 * @method FiltreRecherche[]    findAll()
 * @method FiltreRecherche[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FiltreRechercheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FiltreRecherche::class);
    }

    // /**
    //  * @return FiltreRecherche[] Returns an array of FiltreRecherche objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FiltreRecherche
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
