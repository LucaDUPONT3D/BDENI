<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'nom',
                TextType::class,
                ['label' => 'Nom de la sortie : ']
            )
            ->add(
                'dateHeureDebut',
                DateType::class,
                ['label' => 'Date et Heure de la Sortie : ', 'html5' => true, 'widget' => 'single_text']
            )
            ->add(
                'dateLimiteInscription',
                DateType::class,
                ['label' => 'Date limite d\'incription : ', 'html5' => true, 'widget' => 'single_text']
            )
            ->add(
                'nbInsriptionsMax',
                NumberType::class,
                ['label' => 'Nombre de places : ']
            )
            ->add(
                'duree',
                NumberType::class,
                ['label' => 'Durée : ']
            )
            ->add(
                'infosSortie',
                TextareaType::class,
                ['label' => 'Description et infos : ']
            )
            ->add(
                'campus',
                EntityType::class,
                ['class' => Campus::class,
                    'choice_label' => 'nom',
                    'query_builder' => function (CampusRepository $campusRepository) {
                        return $campusRepository->createQueryBuilder('c');
                    },
                    'label' => 'Campus : ']
            )
            ->add(
                'ville',
                EntityType::class,
                ['class' => Ville::class,
                    'mapped' => false,
                    'choice_label' => 'nom',
                    'query_builder' => function (VilleRepository $villeRepository) {
                        return $villeRepository->createQueryBuilder('v');
                    },
                    'label' => 'Ville : ']
            )
            ->add(
                'lieu',
                EntityType::class,
                ['class' => Lieu::class,
                    'mapped' => false,
                    'choice_label' => 'nom',
                    'query_builder' => function (LieuRepository $lieuRepository) {
                        return $lieuRepository->createQueryBuilder('l');
                    },
                    'label' => 'Lieu : ']
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
