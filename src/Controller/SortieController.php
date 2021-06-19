<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\FiltreRechercheType;
use App\Form\SiteType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;



class SortieController extends AbstractController
{
    /**
     * Liste des sorties
     * @Route("/", name="sortie_liste")
     * @param SortieRepository $sortieRepo
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function liste(SortieRepository $sortieRepo, Request $request)
    {
    	//Création du mini-formulaire pour le filtre du site
	    $formFiltreRecherche = $this->createForm(FiltreRechercheType::class);
	    $formFiltreRecherche->handleRequest($request);

	    if ($formFiltreRecherche-> isSubmitted() && $formFiltreRecherche -> isValid()){

			//Récupération du résultat du filtre des sites
	    	$siteId = $formFiltreRecherche->getData()->getSiteId();

	    	//Récupération du contenu du champ de recherche textuel
		    $recherche = $formFiltreRecherche->getData()->getRecherche();

		    //Récupération du résultat du champ de début de recherche temporelle
		    $dateDebutRecherche = $formFiltreRecherche->getData()->getDateDebutRecherche();

		    //Récupération du résultat du champ de fin de recherche temporelle
		    $dateFinRecherche = $formFiltreRecherche->getData()->getDateFinRecherche();

		    //Récupération du résultat de la checkbox User est organisateur
		    $checkOrganisateur = $formFiltreRecherche->getData()->getCheckOrganisateur();

		    //Récupération du résultat de la checkbox User est inscrit
		    $checkInscrit = $formFiltreRecherche->getData()->getCheckUserInscrit();

		    //Récupération du résultat de la checkbox User n'est pas inscrit
		    $checkPasInscrit = $formFiltreRecherche->getData()->getCheckUserPasInscrit();

		    //Récupération du résultat de la checkbox sorties passées
		    $checkSortiesPassees = $formFiltreRecherche->getData()->getCheckSortiesPassees();

	    }else{
	    	$siteId = null;
	    	$recherche = null;
	    	$dateDebutRecherche = null;
	    	$dateFinRecherche = null;
	    	$checkOrganisateur = null;
	    	$checkInscrit = null;
	    	$checkPasInscrit = null;
	    	$checkSortiesPassees = null;
	    }


	    $user = $this->getUser();
    	$idUser = $user->getId();


    	//Ancienne méthode de récupération des informations de la page de filtres
//    	$recherche = $request->query->get("recherche");
//	   	$dateDebutRecherche = $request->get("dateDebutRecherche");
//    	$dateFinRecherche = $request->get("dateFinRecherche");
//    	$checkOrganisateur = $request->get("checkOrganisateur");
//	    $checkInscrit = $request->get("checkInscrit");
//	    $checkPasInscrit = $request->get("checkPasInscrit");
//	    $checkSortiesPassees = $request->get("checkSortiesPassees");


	    //Récupération de la liste des sorties dans la base, des informations connexes dont nous avons besoin et application des filtres s'ils ne sont pas nuls
	    $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
	    $sorties = $sortieRepo->findAllPersonaliser($idUser, $siteId, $recherche, $dateDebutRecherche, $dateFinRecherche, $checkOrganisateur, $checkInscrit, $checkPasInscrit, $checkSortiesPassees);

	    if(count($sorties) ==null){
		    $this->addFlash("danger","Aucune sortie pour cette recherche");
	    }


	    return $this->render('sorties.html.twig', [
        	'sorties' => $sorties, 'user' => $user, 'siteId' => $siteId, 'recherche' => $recherche, 'dateDebutRecherche' => $dateDebutRecherche,
	        'dateFinRecherche' => $dateFinRecherche, 'checkOrganisateur' => $checkOrganisateur,
	        'checkInscrit' => $checkInscrit, 'checkPasInscrit' => $checkPasInscrit, 'checkSortiesPassees' => $checkSortiesPassees, 'formFiltreRecherche' => $formFiltreRecherche->createView()

        ]);
    }

    /**
     * Détail de la sortie
     * @Route("sortie/{id}", name="sortie_detail", requirements={"id"="\d+"},
     *     methods={"GET","POST"})
     */
    public function detail(Request $request, $id){
        $user=$this->getUser();

	    //récupérer la liste des sorties dans la bases ainsi que toutes leurs informations
	    $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);

	    $sorties = $sortieRepo->find($id);
	    if($sorties == null) {
	    	throw $this->createNotFoundException("Sortie inconnu");
	    }
	    return $this->render('sortie/afficherSortie.html.twig', [
		    'sorties' => $sorties,
            'user' =>$user
	    ]);
    }
}
