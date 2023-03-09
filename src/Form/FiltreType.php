<?php

namespace App\Form;

use App\Form\model\Model;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', ChoiceType::class, [
                'choices' => [
                    'Rennes' => 'Rennes',
                    'Quimper' => 'Quimper',
                    'Nantes' => 'Nantes',
                    'Niort' => 'Niort',
                    'Angers' => 'Angers'
                ],
                'label'=> 'Campus',
                'multiple' => false,
                'expanded' => false
            ])

        ->add('recherche', SearchType::class,[
            'required'=>false,
        'label'=>'le nom de la sortie contient : '])

            ->add('entre', DateType::class,[
                'required'=>false,
                'label'=> 'Entre : ',
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('et', DateType::class,[
                'required'=>false,
                'label'=> 'et : ',
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('organisateur', ChoiceType::class, [
                'label'=>false,
                'choices' => [
                    'Sortie dont je suis organisateur' => 'organisateur',
                ],
                'multiple' => true,
                'expanded' => true
            ])

        ->add('inscrit', ChoiceType::class, [
            'label'=>false,
        'choices' => [
            'Sortie au quel je suis inscrit' => 'inscrit',
        ],
        'multiple' => true,
        'expanded' => true
    ])
            ->add('pasInscrit', ChoiceType::class, [
                'label'=>false,
                'choices' => [
                    'Sortie au quel je ne suis pas inscrit' => 'pasInscrit',

                ],
                'multiple' => true,
                'expanded' => true
            ])
            ->add('passe', ChoiceType::class, [
                'label'=>false,
                'choices' => [
                    'Sortie Passé' => 'passe',
                ],
                'multiple' => true,
                'expanded' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Model::class
        ]);
    }
}
