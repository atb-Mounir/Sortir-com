<?php

namespace App\Repository;

use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Participant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participant[]    findAll()
 * @method Participant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participant::class);
    }






    /*
    public function findOneBySomeField($value): ?Participant
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByPseudoOrEmail($pseudo, $email){
        {
            // on crée un objet QueryBuilder
            $qb = $this->createQueryBuilder('p');
            $qb->andWhere('p.pseudo = :pseudo OR p.mail = :email')
                ->setParameter('pseudo', $pseudo)
                ->setParameter('email', $email);


            // On crée l'objet Query
            $query = $qb->getQuery();

            // On retourne le résultat
            return new Paginator($query);
        }
    }
}
