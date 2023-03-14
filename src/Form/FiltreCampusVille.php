<?php

namespace App\Form;

use App\Entity\User;
use App\Form\model\ModelCampusVille;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreCampusVille extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'recherche',
                SearchType::class,
                ['label'=> 'Le nom contient : ',
                    'attr'=>['placeholder' => 'Entrez un mot...', 'class'=> 'form-control']]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ModelCampusVille::class
        ]);
    }
}
