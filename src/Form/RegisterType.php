<?php

namespace App\Form;

use App\Entity\Participant;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom', 'attr' => ['placeholder' => "Le nom du nouvel utilisateur"]] )
            ->add('prenom', TextType::class, ['label' => 'Prenom', 'attr' => ['placeholder' => "Le prénom du nouvel utilisateur"]])
            ->add('telephone', TextType::class, ['label' => 'Telephone', 'attr' => ['placeholder' => "Le numéro du nouvel utilisateur"]])
            ->add('mail', EmailType::class, ['label' => 'Email', 'attr' => ['placeholder' => "L'adresse email du nouvel utilisateur"]])
            ->add('pseudo', TextType::class, ['label' => 'Pseudo', 'attr' => ['placeholder' => "Le pseudo du nouvel utilisateur"]])
            ->add('photo', FileType::class, ['label' => 'Choisir une photo de profil'])
            ->add('motDePasse', RepeatedType::class, [ 'type' => PasswordType::class,
								                                    'invalid_message' => "Les mots de passes ne sont pas identique!",
								                                    'first_options' => ['label' => 'Mot de passe'],
								                                    'second_options' => ['label'=>'Confirmation du mot de passe']
							                                         ])
            ->add('administrateur',CheckboxType::class, [
                'label' => "Administrateur ",
                'required' => false
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
