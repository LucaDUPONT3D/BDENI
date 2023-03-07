<?php

namespace App\Form;

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
        'label'=>'le nom de la sortie contient : '])
            ->add('entre', DateType::class,[
                'label'=> 'Entre : ',
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('et', DateType::class,[
                'label'=> 'et : ',
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('critere', ChoiceType::class, [
                'label'=>'',
                'choices' => [
                    'Sortie dont je suis organisateur' => 'Sortie dont je suis organisateur',
                    'Sortie au quel je suis inscrit' => 'Sortie au quel je suis inscrit',
                    'Sortie au quel je ne suis pas inscrit' => 'Sortie au quel je ne suis pas inscrit',
                    'Sortie Passé' => 'Sortie Passé',
                ],
                'multiple' => true,
                'expanded' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
