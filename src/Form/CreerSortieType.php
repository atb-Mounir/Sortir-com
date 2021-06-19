<?php

namespace App\Form;

use App\Entity\Sortie;
use Cassandra\Numeric;
use DateTimeImmutable;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeImmutableToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreerSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom de la sortie', 'attr' => ['placeholder' => "Nom de la sortie"]] )
            ->add('dateHeureDebut', DateTimeType::class, ['label' => 'Date et heure de la sortie'])
            ->add('duree', IntegerType::class, ['label' => 'Durée en heure'])
            ->add('dateLimiteInscription', DateType::class, ['label' => 'Date limite d\'inscription'])
            ->add('nbInscriptioonsMax', IntegerType::class, ['label' => 'Nombre de places'])
            ->add('infosSortie', TextareaType::class, ['label' => 'Description et info'])
            /*->add('site', EntityType::class, ['class' => "App\Entity\Site", 'choice_label' => "Nom", 'attr' => ['disabled' => 'disabled']])*/
            /*->add('site', EntityType::class,['class' => "App\Entity\Site",
                'choice_label' => "Nom",
                'placeholder' => "Sélectionner une ville",
                'attr' => ['disabled' => 'disabled'],
                'expanded' => false,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.nom', 'desc');
                }] )*/
            ->add('lieu', EntityType::class,['class' => "App\Entity\Lieu",
                'choice_label' => "Nom",
                'placeholder' => "Sélectionner un lieu",
                'expanded' => false,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('l')
                        ->orderBy('l.nom', 'desc');
                }])
            ->add('enregistrer', SubmitType::class, ['label' => 'Enregistrer'])
            ->add('publier', SubmitType::class, ['label' => 'Publier'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
