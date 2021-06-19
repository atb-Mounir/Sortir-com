<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Site;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ParticipantController extends AbstractController
{
	/**
	 * Créer un nouvel utilisateur
	 * @Route("/register", name="participant_registrer")
	 * @param Request $request
	 * @param EntityManagerInterface $em
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function register(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
	{
		$participant = new Participant();
		$registerForm = $this->createForm(RegisterType::class, $participant);
		$registerForm->handleRequest($request);

		if ($registerForm->isSubmitted() && $registerForm->isValid()){

		    //Recuperation du fichier upload
            $file = $participant->getPhoto();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('upload_directory'), $fileName);
            $participant->setPhoto($fileName);

		    //Vérification de l'unicité du pseudo et de l'email
            $participantRepo = $this->getDoctrine()->getRepository(Participant::class);
            $participantDejaExiste = $participantRepo->findByPseudoOrEmail($participant->getPseudo(), $participant->getMail());
            if(count($participantDejaExiste)!=null){
                $this->addFlash("danger", "Le pseudo et/ou l'email existe déjà");
                return $this->render('participant/register.html.twig', ['registerForm' => $registerForm->createView()]);
            }

            $password = $encoder->encodePassword($participant, $participant->getPassword());
			$participant->setMotDePasse($password);
			$participant->setActif(1);

			$site = new Site();
			$site->setNom("ENI-Rennes");
			$em->persist($site);

			$participant->setSite($site);

			$em->persist($participant);
			$em->flush();
            $this->addFlash("success", "Nouvel utilisateur enregistré");
			return $this->redirectToRoute("sortie_liste");
		}

		return $this->render('participant/register.html.twig', ['registerForm' => $registerForm->createView()]);
	}

	/**
	 * Récupére un participant en BDD par son ID pour l'affichage du profil
	 * @Route("/profil/{id}", name="participant_autreProfil",
	 *  methods={"GET", "POST"})
	 * @param Request $request
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function autreProfil(request $request ,$id)
	{
		//récupérer le detail de l'article dans la BDD
		$participantRepo = $this->getDoctrine()->getRepository(Participant::class);
		$participant = $participantRepo->find($id);
		if ($participant == null){
			throw $this->createNotFoundException("Profil inconnu");
		}
		return $this->render("participant/afficherParticipant.html.twig", ['participant' => $participant]);
	}

    /**
     * Détail de l'article
     * @Route("/monProfil", name="participant_monProfil",
     *     methods={"GET", "POST"})
     */
    public function monProfil(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em) {

        //Récupération de l'utilisateur courant
        $user = $this->getUser();
        //On récupére le nom de la photo de l'utilisateur
        $photoUser = $user->getPhoto();
        //On met le champ photo du user à null pour pouvoir afficher le formulaire
        $user->setPhoto(null);

        $registerForm = $this->createForm(RegisterType::class, $user);
        $registerForm->handleRequest($request);
        if ($registerForm->isSubmitted() && $registerForm->isValid()){
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setMotDePasse($password);
            $user->setMotDePasse($password);
            $user->setActif(1);

            //On verifie si l'utilisateur à ajouter une nouvelle photo sinon on replace l'ancienne
            if($user->getPhoto() == null){
                $user->setPhoto($photoUser);
            }else{
                //Recuperation du fichier upload
                $file = $user->getPhoto();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move($this->getParameter('upload_directory'), $fileName);
                $user->setPhoto($fileName);
            }

            $site = new Site();
            $site->setNom("ENI-Rennes");
            $em->persist($site);

            $user->setSite($site);
            //Mise à jour du profil
            $em->flush();
            return $this->redirectToRoute("sortie_liste");
        }

        return $this->render('participant/monProfil.html.twig', [
            'registerForm' => $registerForm->createView(),
            'user' => $user
            ]);

    }
}


