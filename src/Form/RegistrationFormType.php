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

            ->add('pseudo', TextType::class,[
                'label' => 'Pseudo',
                'trim' => true,
                'required' => true,
            ])
            ->add('nom', TextType::class,[
                'label' => 'Nom',
                'trim' => true,
                'required' => true,
            ])
            ->add('prenom', TextType::class,[
                'label' => 'Prenom',
                'trim' => true,
                'required' => true,
            ])
            ->add('img', FileType::class,[
                'label' => 'Photo',
                'mapped' => false,
                'required' => false,
                'constraints'=>[
                    new File ([
                        'maxSize' => '2500k',
                        'mimeTypesMessage' =>"l'image n'est pas valide"

                    ])
                ]

            ])
            ->add('actif',CheckboxType::class,[
                'data'=>true
            ])
            ->add('telephone',TextType::class,[
                "trim" => true,
                "label" => "Téléphone",
                "required" => false,
            ])
            ->add('email', EmailType::class,[
                'label' => 'Email',
                'trim' => 'true'
            ])
            ->add('roles',CollectionType::class, [
                'entry_type' => ChoiceType::class,
                'label' => "Role de l'utilisateur",
                'entry_options' => [
                    'label' => false,
                    'choices'=> [
                        'User' => 'ROLE_USER',
                        'Admin' => 'ROLE_ADMIN'
                    ],
                ],
            ])
            ->add('campus',EntityType::class,[
                'class' => Campus::class,
                'choice_label' => 'nom',
                'label' => 'Campus',
                'trim' => true,
                'attr'=> array('class'=>'form-control')
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                'trim'=>true,
                'label' => 'Confirmation',
                'constraints' => [

                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit être au moins de {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
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
