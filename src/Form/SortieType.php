<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{

    public function __construct(private LieuRepository $lieuRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'nom',
                TextType::class,
                ['label' => 'Nom de la sortie : ',
                    'attr' => ['placeholder' => 'Entrez un nom ...','class' => 'form-control']
                ]
            )
            ->add(
                'dateHeureDebut',
                DateTimeType::class,
                ['label' => 'Date et Heure de la Sortie : ', 'html5' => true, 'widget' => 'single_text', 'attr' => ['class' => 'form-control']]
            )
            ->add(
                'dateLimiteInscription',
                DateType::class,
                ['label' => 'Date limite d\'incription : ', 'html5' => true, 'widget' => 'single_text', 'attr' => ['class' => 'form-control']]
            )
            ->add(
                'nbInsriptionsMax',
                NumberType::class,
                ['label' => 'Nombre de places : ',
                    'attr' => ['placeholder' => 'Entrez un nombre de places ...','class' => 'form-control']
                ]
            )
            ->add(
                'duree',
                NumberType::class,
                ['label' => 'Durée (minutes) : ',
                    'attr' => ['placeholder' => 'Entrez une durée ...','class' => 'form-control']
                ]
            )
            ->add(
                'infosSortie',
                TextareaType::class,
                ['label' => 'Description et infos : ',
                    'attr' => ['placeholder' => 'Entrez une description ...','class' => 'form-control']
                ]
            )
            ->add(
                'campus',
                EntityType::class,
                ['class' => Campus::class,
                    'choice_label' => 'nom',
                    'placeholder' => 'Choisissez un campus ...',
                    'query_builder' => function (CampusRepository $campusRepository) {
                        return $campusRepository->createQueryBuilder('c');
                    },
                    'label' => 'Campus : ', 'attr' => ['class' => 'form-control']]
            )
            ->add(
                'ville',
                EntityType::class,
                ['class' => Ville::class,
                    'mapped' => false,
                    'choice_label' => 'nom',
                    'placeholder' => 'Choisissez une ville ...',
                    'query_builder' => function (VilleRepository $villeRepository) {
                        return $villeRepository->createQueryBuilder('v');
                    },
                    'label' => 'Ville : ', 'attr' => ['class' => 'form-control']]
            )
            ->add(
                'lieu',
                EntityType::class,
                ['class' => Lieu::class,
                    'choice_label' => 'nom',
                    'label' => 'Lieu : ',
                    'placeholder' => 'Choisissez un lieu ...',
                    'choices' => [], 'attr' => ['class' => 'form-control']]
            );

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        // Search for selected Ville and convert it into an Entity
        $lieux = $this->lieuRepository->findBy(['ville' => $data['ville']]);

        $form->add('lieu', EntityType::class, array(
            'required' => true,
            'choice_label' => 'nom',
            'choices' => $lieux,
            'placeholder' => 'Choisissez un lieu ...',
            'class' => Lieu::class, 'attr' => ['class' => 'form-control']
        ));
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $sortie = $event->getData();
        $lieu = $sortie->getLieu();

        if ($lieu) {
            $ville = $lieu->getVille();
            $lieux = $this->lieuRepository->findBy(['ville' => $ville]);

            if ($ville) {
                $form->add(
                    'ville',
                    EntityType::class,
                    ['class' => Ville::class,
                        'mapped' => false,
                        'data' => $ville,
                        'choice_label' => 'nom',
                        'placeholder' => 'Choisissez une ville ...',
                        'query_builder' => function (VilleRepository $villeRepository) {
                            return $villeRepository->createQueryBuilder('v');
                        },
                        'label' => 'Ville : ', 'attr' => ['class' => 'form-control']]
                );

                $form->add('lieu', EntityType::class, array(
                    'required' => true,
                    'choice_label' => 'nom',
                    'data' => $lieu,
                    'choices' => $lieux,
                    'placeholder' => 'Choisissez un lieu ...',
                    'class' => Lieu::class, 'attr' => ['class' => 'form-control']
                ));
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required'=>false,
            'data_class' => Sortie::class,
        ]);
    }

    public function getBlockPrefix()
    {

        return 'appbundle_sortie';
    }
}
