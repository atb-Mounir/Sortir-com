<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SortirFixtures extends Fixture
{
    public function load(ObjectManager $manager){
        // Instancier Faker
        $faker = \Faker\Factory::create('fr_FR');

        //Remplissage de la table Site
        $site1 = new Site();
        $site1->setNom("ENI-Chartres De Bretagne");
        $manager->persist($site1);
        $site2 = new Site();
        $site2->setNom("ENI-Nantes Faraday");
        $manager->persist($site2);
        $site3 = new Site();
        $site3->setNom("ENI-Quimper");
        $manager->persist($site3);

        //Remplissage de la table Etat
        $etat1 = new Etat();
        $etat1->setLibelle("Créée");
        $etat1->setId(1);
        $manager->persist($etat1);
        $etat2 = new Etat();
        $etat2->setLibelle("Ouverte");
        $etat2->setId(2);
        $manager->persist($etat2);
        $etat3 = new Etat();
        $etat3->setLibelle("Clôturée");
        $etat3->setId(3);
        $manager->persist($etat3);
        $etat4 = new Etat();
        $etat4->setLibelle("Activité en cours");
        $etat4->setId(4);
        $manager->persist($etat4);
        $etat5 = new Etat();
        $etat5->setLibelle("Passée");
        $etat5->setId(5);
        $manager->persist($etat5);
        $etat6 = new Etat();
        $etat6->setLibelle("Annulée");
	    $etat6->setId(6);
        $manager->persist($etat6);

        //Remplissage de la table Ville
        for ($v = 1; $v <= 5; $v++ ) {
            $ville = new Ville();
            $ville->setNom($faker->city);
            $ville->setCodePostal($faker->postcode);
            $manager->persist($ville);

            //Remplissage de la table Lieu
            for ($l = 1; $l <= 3; $l++ ) {
                $lieu = new Lieu();
                $lieu->setNom($faker->word);
                $lieu->setRue($faker->streetAddress);
                $lieu->setVille($ville);
                $manager->persist($lieu);

                //Remplissage de la table Participant
                for ($p = 1; $p <= 5; $p++ ) {
                    $participant = new Participant();
                    $participant->setNom($faker->lastName);
                    $participant->setPrenom($faker->firstName);
                    $participant->setPseudo($faker->userName);
                    $participant->setTelephone($faker->phoneNumber);
                    $participant->setMail($faker->email);
                    $participant->setMotDePasse($faker->password);
                    $participant->setAdministrateur(0);
                    $participant->setActif(1);
                    $participant->setSite($site1);
                    $manager->persist($participant);

                    //Remplissage de la table Sortie
                    for ($s = 1; $s <= 3; $s++ ) {
                        $time = "05:45";
                        $date = "01-09-2015";

                        $sortie = new Sortie();
                        $sortie->setNom($faker->word);
                        $sortie->setDateHeureDebut($faker->dateTimeBetween('-6 months', '+6months'));
                        $sortie->setDuree($faker->numberBetween(30, 240));
                        $sortie->setDateLimiteInscription($faker->dateTimeBetween('-12 months', $sortie->getDateHeureDebut()));
                        $sortie->setNbInscriptioonsMax($faker->numberBetween(2, 10));
                        $sortie->setInfosSortie("$faker->sentence");
                        $sortie->setEtat($etat1);
                        $sortie->setOuverteOuNon($faker->numberBetween(0,1));
                        $sortie->setLieu($lieu);
                        $sortie->setSite($site1);
                        $sortie->setOrganisateur($participant);
                        $sortie->addRelation($participant);

                        $manager->persist($sortie);
                    }
                }
            }
        }
        $manager->flush();
    }
}
