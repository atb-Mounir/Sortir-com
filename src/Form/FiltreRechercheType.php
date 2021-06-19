<?php

namespace App\Form;

use App\Entity\FiltreRecherche;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreRechercheType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			-> add('siteId', EntityType::class, [
				'class' => Site::class,
				'choice_label' => 'nom',
				'placeholder' => "-- Filtrer par Site --",
				'required' => false
			])
			->add('recherche', TextType::class, [
				'label' => "Le nom de la sortie contient: ",
				'required' => false
			])
			-> add('dateDebutRecherche', DateType::class, [
				'label' => "Entre le ",
				'required' => false
			])
			-> add('dateFinRecherche', DateType::class, [
				'label' => "Et le ",
				'required' => false
			])
			-> add('checkOrganisateur',  CheckboxType::class, [
				'label' => "Sorties dont je suis l'organisateur/trice ",
				'required' => false
			])
			-> add('checkUserInscrit', CheckboxType::class, [
				'label' => "Sorties auxquelles je suis inscrit/e ",
				'required' => false
			])
			-> add('checkUserPasInscrit', CheckboxType::class, [
				'label' => "Sorties auxquelles je ne suis pas inscrit/e ",
				'required' => false
			])
			-> add('checkSortiesPassees', CheckboxType::class, [
				'label' => "Sorties passées ",
				'required' => false
			]);

	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver -> setDefaults([
			'data_class' => FiltreRecherche::class,
		]);
	}
}
