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
                'label' => 'Campus',
                'multiple' => false,
                'placeholder' => 'Choisissez un campus ...',
                'expanded' => false,
                'attr'=>  ['class' => 'form-control']
            ])
            ->add('recherche', SearchType::class, [
                'required' => false,
                'attr' => ['placeholder'=>'Entrez un mot...', 'class' => 'form-control'],
                'label' => 'Le nom de la sortie contient : '])
            ->add('entre', DateType::class, [
                'required' => false,
                'label' => 'Entre : ',
                'html5' => true,
                'widget' => 'single_text',
                'attr'=>  ['class' => 'form-control']
            ])
            ->add('et', DateType::class, [
                'required' => false,
                'label' => 'et : ',
                'html5' => true,
                'widget' => 'single_text',
                'attr'=>  ['class' => 'form-control']
            ])
            ->add('organisateur', ChoiceType::class, [
                'attr' => ['class'=>'checkBoxSpace'],
                'label' => false,
                'choices' => [
                    'Je suis organisateur(trice)' => 'organisateur',
                ],
                'multiple' => true,
                'expanded' => true
            ])
            ->add('inscrit', ChoiceType::class, [
                'attr' => ['class'=>'checkBoxSpace'],
                'label' => false,
                'choices' => [
                    'Je suis inscrit(e)' => 'inscrit',
                ],
                'multiple' => true,
                'expanded' => true
            ])
            ->add('pasInscrit', ChoiceType::class, [
                'attr' => ['class'=>'checkBoxSpace'],
                'label' => false,
                'choices' => [
                    'Je ne suis pas inscrit(e)' => 'pasInscrit',

                ],
                'multiple' => true,
                'expanded' => true
            ])
            ->add('passe', ChoiceType::class, [
                'attr' => ['class'=>'checkBoxSpace'],
                'label' => false,
                'choices' => [
                    'Sortie Passées' => 'passe',
                ],
                'multiple' => true,
                'expanded' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Model::class,
            'required' => false
        ]);
    }
}
