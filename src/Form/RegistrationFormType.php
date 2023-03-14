<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Doctrine\DBAL\Types\BooleanType;
use PharIo\Manifest\Email;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'trim' => true,
                'required' => false,
                'attr' => ['class' => 'form-control',
                    'placeholder' => 'Entrez un email...']
            ])
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
                'trim' => true,
                'required' => true,
                'attr' => ['class' => 'form-control',
                    'placeholder' => 'Entrez un pseudo...']
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prenom',
                'trim' => true,
                'required' => true,
                'attr' => ['class' => 'form-control',
                    'placeholder' => 'Entrez un prénom...']
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'trim' => true,
                'required' => true,
                'attr' => ['class' => 'form-control',
                    'placeholder' => 'Entrez un nom...']
            ])
            ->add('telephone', TextType::class, [
                "trim" => true,
                "label" => "Téléphone",
                "required" => false,
                'attr' => ['class' => 'form-control',
                    'placeholder' => 'Entrez un numéro de téléphone...']
            ])
            ->add('plainPassword', RepeatedType::class, [
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                'trim' => true,
                'label' => 'Confirmation',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
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
                'choice_label' => 'nom',
                'label' => 'Campus',
                'placeholder' => 'Choisissez un campus...',
                'trim' => true,
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'required' => false
        ]);
    }
}
