<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use function Sodium\add;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


//    public function findOneBySomeField($value): ?Sortie
//	    //SELECT COUNT(participant_id) FROM `sortie_participant` WHERE sortie_id=1
//    {
//    	$em = $this->getEntityManager();
//    	// on crée la requête DQL
//	    $dql = "SELECT COUNT(participant_id)
//	            FROM sortie_participant
//	            WHERE sortie_id=$value";
//		// on crée un objet Query
//	    $query = $em->createQuery($dql);
//	    // on retourne le résultat
//
//        return $query ->getOneOrNullResult()
//        ;
//    }

	public function findAllPersonaliser($idUser, $siteId, $recherche, $dateDebutRecherche, $dateFinRecherche, $checkOrganisateur, $checkInscrit, $checkPasInscrit, $checkSortiesPassees)
	{
		// on crée un objet QueryBuilder
		$qb = $this->createQueryBuilder('s');
		$qb->addSelect('par')
			->addSelect('si')
			->addSelect('l')
			->addSelect('v')
			->addSelect('e')
			->join('s.organisateur', 'par')
			->join('s.site', 'si')
			->join('s.lieu', 'l')
			->join('s.etat', 'e')
			->join('l.ville', 'v')
			->orderBy("s.dateHeureDebut", "asc");

		//Dropdown des sites
		if (!is_null($siteId)){
			$qb->andWhere('si.id = :siteId')->setParameter('siteId', $siteId);
		}

		//Champ de recherche textuel
		if (!is_null(trim($recherche))){
			$qb->andWhere('s.nom LIKE :recherche')->setParameter('recherche', '%'.$recherche.'%');
		}

		//Champs de recherche par dates:
		//Date de début pour la recherche
		if (!is_null($dateDebutRecherche)){
			$qb->andWhere('s.dateHeureDebut > :dateDebutRecherche')->setParameter('dateDebutRecherche', $dateDebutRecherche);
		}
		//Date de fin pour la recherche
		if (!is_null($dateFinRecherche)){
			$qb->andWhere('s.dateHeureDebut < :dateFinRecherche')->setParameter('dateFinRecherche', $dateFinRecherche);
		}

		//Checkboxes:
		//Checkbox User est organisateur de la sortie
		if ($checkOrganisateur == 'true'){
			$qb->andWhere('par.id = :id')->setParameter('id', $idUser);
		} else {
			$qb->andWhere("not (e.id = 1 and par.id != :id)") ->setParameter("id", $idUser);
		}
		//Checkbox User est inscrit à la sortie
		if ($checkInscrit == 'true'){
			$qb ->join('s.relation', 'r')
				->andWhere('r.id = :id')->setParameter('id', $idUser);
		}
		//Checkbox User n'est pas inscrit à la sortie
		if ($checkPasInscrit == 'true'){
			$qb ->join('s.relation', 'r')
				->andWhere('r.id != :id')->setParameter('id', $idUser);
		}
		//Checkbox la Sortie est passée
		if ($checkSortiesPassees == 'true'){
			$qb ->andWhere("s.dateHeureDebut < DATE_SUB(CURRENT_DATE(),1, 'month')" );
		} else {
			$qb ->andWhere("s.dateHeureDebut > DATE_SUB(CURRENT_DATE(),1, 'month')" );

		}




		// On crée l'objet Query
		$query = $qb->getQuery();

		// On retourne le résultat
		return new Paginator($query);
	}




}
