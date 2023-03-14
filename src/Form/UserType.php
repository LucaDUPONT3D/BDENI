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
use Symfony\Component\Validator\Constraints\Length;
use function Sodium\add;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
                'attr'=>[
                    'placeholder' =>'Entrez votre pseudo',
                    'class'=> 'form-control'
                ],
                'trim' => true,
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prenom',
                'attr'=>[
                    'placeholder' =>'Entrez votre prenom',
                    'class'=> 'form-control'
                ],
                'trim' => true,

            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr'=>[
                    'placeholder' =>'Entrez votre nom',
                    'class'=> 'form-control'
                ],
                'trim' => true,

            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'attr'=>[
                    'placeholder' =>'Entrez votre numéro de téléphone',
                    'class'=> 'form-control'
                ],
                'trim' => true,

            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr'=>[
                    'placeholder' =>'Entrez votre email',
                    'class'=> 'form-control'
                ],
                'trim' => true,


            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'attr'=>[
                    'placeholder' =>'Entrez votre nouveau mot de passe',
                    'class'=> 'form-control'
                ],
                'mapped' => false,
                'trim'=>true,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit être au moins de {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],

            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'label' => 'Campus',
                'choice_label' => 'nom',
                'attr'=>[
                    'class'=> 'form-control'
                ],
                'trim' => true,
            ])
            ->add('image', FileType::class, [
                'label' => 'Photo',
                'attr'=>[
                    'placeholder' =>'Chargez votre photo de profil',
                    'class'=> 'form-control'
                ],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File ([
                        'maxSize' => '5000k',
                        'mimeTypesMessage' => 'Image non valide !',
                    ])
                ],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'required'=>false
        ]);
    }
}
