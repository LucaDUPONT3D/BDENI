<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('rue')
            ->add('latitude')
            ->add('longitude')
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
