<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\AnnulerType;
use App\Form\CreerSortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GestionEtatSortieController extends AbstractController
{

    /**
     * Methode pour créer une nouvelle sortie avec possibilité de la publier directement
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/sortie/creer", name="gestionEtat_creer", methods={"GET", "POST"})
     */
    public function creerSortie(Request $request, EntityManagerInterface $em)
    {
        //On récupére l'utilisateur connecté
        $participant = $this->getUser();
        //On recupére l'objet site de l'utiilisateur
        $siteOrganisateur = $participant->getSite();
        //On récupére la ville de l'objet site de l'utilisateur
        $villeOrganisatrice = $siteOrganisateur->getNom();

        $sortie = new Sortie();
        $sortie->setSite($siteOrganisateur);
        $creerSortieForm = $this->createForm(CreerSortieType::class, $sortie);
        $creerSortieForm->handleRequest($request);

        if ($creerSortieForm->isSubmitted() && $creerSortieForm->isValid()){
            //On récupére l'etat en fonction du bouton cliqué
            $etatRepo = $this->getDoctrine()->getRepository(Etat::class);
            if ($creerSortieForm->get('enregistrer')->isClicked()){
                $etat = $etatRepo->find(1);
                $sortie->setOuverteOuNon(0);
            }else if($creerSortieForm->get('publier')->isClicked()){
                $etat = $etatRepo->find(2);
                $sortie->setOuverteOuNon(1);
            }

            //on ajoute les champs manquants du formulaire dans l'objet Sortie
            $sortie->setEtat($etat);
            $sortie->setOrganisateur($participant);
            $sortie->setSite($siteOrganisateur);

            $em->persist($sortie);
            if($sortie->getDateLimiteInscription() < $sortie->getDateHeureDebut()){
                if($sortie->getEtat()->getId() == 1){
                    $this->addFlash("success", "Sortie enregistrée");
                }elseif($sortie->getEtat()->getId() == 2){
                    $this->addFlash("success", "Sortie publiée");
                }
                $em->flush();
                return $this->redirectToRoute("sortie_liste");
            }else{
                $this->addFlash("danger", "La date de limite d'incription ne peut pas être supérieure à la date de l'évenement.");
            }

        }
        return $this->render('sortie/gestionSortie.html.twig', [
            'creerSortieForm' => $creerSortieForm->createView(),
            'participant' => $participant ]);
    }

    /**
     * Methode pour modifier une sortie enregistrée. Possibilité de la publier ou de la supprimer
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/sortie/modifier/{id}", name="gestionEtat_modifier", requirements={"id"="\d+"})
     */
    public function modificationSortie(Request $request, EntityManagerInterface $em, $id)
    {
        //On récupére l'utilisateur connecté
        $participant = $this->getUser();

        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);
        $modificationSortieForm = $this->createForm(CreerSortieType::class, $sortie);
        $modificationSortieForm->handleRequest($request);

        if ($modificationSortieForm->isSubmitted()
            && $modificationSortieForm->isValid()
            && ($participant->getId() == $sortie->getOrganisateur()->getId())){
            //On récupére l'etat en fonction du bouton cliqué
            $etatRepo = $this->getDoctrine()->getRepository(Etat::class);
            if ($modificationSortieForm->get('enregistrer')->isClicked()){
                $etat = $etatRepo->find(1);
                $sortie->setOuverteOuNon(0);
                $this->addFlash("success", "Modification de la sortie enregistrée");
            }else if($modificationSortieForm->get('publier')->isClicked()){
                $etat = $etatRepo->find(2);
                $sortie->setOuverteOuNon(1);
                $this->addFlash("success", "Sortie publiée");
            }
            $sortie->setEtat($etat);
            $em->persist($sortie);
            $em->flush();
            return $this->redirectToRoute("sortie_liste");
        }

        return $this->render('sortie/gestionSortie.html.twig', ['creerSortieForm' => $modificationSortieForm->createView(), 'sortie' => $sortie, 'participant' => $participant ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/sortie/supprimer/{id}", name="gestionEtat_supprimer", requirements={"id"="\d+"})
     */
    public function supprimerSortie(Request $request, EntityManagerInterface $em, $id)
    {
        //On récupére l'utilisateur connecté
        $utilisateur = $this->getUser();

        //On récupére l'article en BDD
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);

        if($sortie==null){
            throw $this->createNotFoundException("Sortie inconnue ou déjà supprimée");
        }
        if(($sortie->getEtat()->getId() == 1) && ($utilisateur->getId() == $sortie->getOrganisateur()->getId())) {
            //suppression en BDD
            $em->remove($sortie);
            $em->flush();
            $this->addFlash("success", "Sortie supprimée");
        }
        return $this->redirectToRoute("sortie_liste");
    }

    /**
     * @Route("/sortie/publier/{id}", name="gestionEtat_publier", requirements={"id"="\d+"})
     */
    public function publier($id, Request $request, EntityManagerInterface $em){

        //On récupére l'utilisateur connecté
        $utilisateur = $this->getUser();

        //Récupération de l'objet sortie
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);
        if($sortie == null) {
            throw $this->createNotFoundException("Sortie inconnu");
        }

        //Mise à jour de l'état en mode "ouverte" seulement si l'état précédent est "créé"
        if($sortie->getEtat()->getId()==1
            && ($utilisateur->getId() == $sortie->getOrganisateur()->getId())) {
            $etatRepo=$this->getDoctrine()->getRepository(Etat::class);
            $etat=$etatRepo->find(2);
            $sortie->setOuverteOuNon(1);
            $sortie->setEtat($etat);
            $em->persist($sortie);
            $em->flush();
            $this->addFlash("success", "Sortie publiée");
        }
        //Redirection vers la fonction controlleur d'affichage globale
        return $this->redirectToRoute("sortie_liste");
    }

    /**
     * @Route("/sortie/annuler/{id}", name="gestionEtat_annuler", requirements={"id"="\d+"})
     */
    public function annuler($id, Request $request, EntityManagerInterface $em)
    {

        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);

        if($sortie == null) {
            throw $this->createNotFoundException("Sortie inconnu");
        }

        $sortieForm = $this->createForm(AnnulerType::class, $sortie);
        $sortieForm->handleRequest($request);
        //Pour annuler, la sortir doit être en état ouverte avec un motif d'annulation d'inséré
        if ($sortieForm->isSubmitted()
            && $sortieForm->isValid()
            && $sortieForm->get("motifAnnulation")->getData() !=null
            && $sortie->getOuverteOuNon()==true
            ){

            $etatRepo=$this->getDoctrine()->getRepository(Etat::class);
            $etat=$etatRepo->find(6);
            $sortie->setEtat($etat);
            $em->persist($sortie);
            //Mise à jour de la sortie en mode annulation
            $em->flush();

            $this->addFlash("success", "La sortie a été annulée");
            return $this->redirectToRoute("sortie_liste");
        }
        else if($sortieForm->isSubmitted() && $sortieForm->isValid() &&
            $sortieForm->get("motifAnnulation")->getData() ==null){
            $this->addFlash("danger", "Le motif d'annulation est obligatoire.");
        }

        return $this->render('sortie/annulerSortie.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'sortie' => $sortie
        ]);
    }

    /**
     * Inscrire l'utilisateur à une sortie
     * @Route("/sortie/inscrire/{id}", name="gestionEtat_inscrire", requirements={"id"="\d+"})
     */
    public function inscrireParticipant($id, EntityManagerInterface $em, Request $request)
    {
        //Récupération de l'utilisateur courant
        $user = $this->getUser();
        //Récupérer la liste des participants de la sortie
        $sortie = $em->getRepository(Sortie::class)->find($id);

        if ($sortie==null){
            throw $this->createNotFoundException("Sortie inconnue");
        }

        // Ajouter une nouvelle relation à la sortie
        $sortie->addRelation($user);

        // sauvegarder les données dans la base
        $em->persist($sortie);
        $em->flush();
        // ajout d'un message pour l'utilisateur
        $this->addFlash("success", "Vous êtes bien inscrit à la sortie : )");

        // modification de l'état de la sortie si le nbInscritMax est atteint
        if ($sortie->getNbInscriptioonsMax()==count($sortie->getRelation())){
            // cloturer la sortie

            $etatRepo=$this->getDoctrine()->getRepository(Etat::class);
            $etat=$etatRepo->find(3);
            $sortie->setEtat($etat);
            //sauvegarde en BDD
            $em->persist($sortie);
            $em->flush();
        }
        // redirection
        return $this->redirectToRoute("sortie_detail",[
            'id' =>$sortie->getId()
        ]);
    }

    /**
     * Inscrire l'utilisateur à une sortie
     * @Route("/sortie/desister/{id}", name="gestionEtat_desister", requirements={"id"="\d+"})
     */
    public function desisteParticipant($id, EntityManagerInterface $em, Request $request)
    {
        //Récupération de l'utilisateur courant
        $user = $this->getUser();

        //Récupérer la liste des participants de la sortie
        $sortie = $em->getRepository(Sortie::class)->find($id);
        if ($sortie==null){
            throw $this->createNotFoundException("Sortie inconnue");
        }

        // Ajouter une nouvelle relation à la sortie
        $sortie->removeRelation($user);

        // sauvegarder les données dans la base
        $em->persist($sortie);
        $em->flush();
        // ajout d'un message pour l'utilisateur
        $this->addFlash("success", "Vous n'êtes plus inscrit à la sortie : )");
        // modification de l'état de la sortie si le nbInscritMax est atteint
        if ($sortie->getNbInscriptioonsMax()>count($sortie->getRelation())){

            $etatRepo=$this->getDoctrine()->getRepository(Etat::class);
            $etat=$etatRepo->find(2);
            $sortie->setEtat($etat);

            //sauvegarde en BDD
            $em->persist($sortie);
            $em->flush();
        }
        // redirection
        return $this->redirectToRoute("sortie_liste",[
            'id' =>$sortie->getId()
        ]);
    }

}
