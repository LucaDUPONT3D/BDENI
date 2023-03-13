<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label'=>'Nom : ','attr' => ['placeholder' => 'Entrez un nom ...']])
            ->add('rue', TextType::class, ['label'=>'Adresse : ','attr' => ['placeholder' => 'Entrez une adresse ...']])
            ->add('latitude', TextType::class, ['label'=>'Latitude : ','attr' => ['placeholder' => 'Entrez une latitude ...']])
            ->add('longitude', TextType::class, ['label'=>'Longitude : ','attr' => ['placeholder' => 'Entrez une longitude ...']])
            ->add(
                'ville',
                EntityType::class,
                ['class' => Ville::class,
                    'choice_label' => 'nom',
                    'placeholder' => 'Choisissez une ville ...',
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
            'required'=> false
        ]);
    }
}
