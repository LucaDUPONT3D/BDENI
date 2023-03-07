<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,[
                'label' => 'Email',
                'trim' => true,
                'required' => false
            ])
            ->add('plainPassword',RepeatedType::class,[
                'type' => PasswordType::class,
                'mapped'=> false,
                'invalid_message'=>'Les mots de passe ne correspondent pas',
                'required' => true,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation mot de passe']
            ])
            ->add('nom',TextType::class,[
                'label' => 'Nom',
                'trim' => true,
                "required" => false
            ])
            ->add('prenom',TextType::class,[
                'label' => 'Prenom',
                'trim' => true,
                "required" => false
            ])
            ->add('telephone',TextType::class,[
                'label' => 'Téléphone',
                'trim' => true,
                "required" => false
            ])
            ->add('image',FileType::class,[
                'label' => 'Photo',
                'mapped' => false ,
                'required' => false ,
                'constraints' => [
                    new File ([
                        'maxSize' => '2500k' ,
                        'mimeTypesMessage' => 'Image non valide !' ,
                    ])
                ],
            ])
            ->add('pseudo',TextType::class,[
                'label' => 'Pseudo',
                'trim'=> true,
                "required" => false,

                ])
            ->add('campus',EntityType::class,[
                'class' => Campus::class,
                'choice_label'=>'nom',
                'label'=>'Campus',
                'trim' =>true,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
