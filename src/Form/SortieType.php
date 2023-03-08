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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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
                    'attr' => ['placeholder' => 'Entrez un nom ...']
                ]
            )
            ->add(
                'dateHeureDebut',
                DateType::class,
                ['label' => 'Date et Heure de la Sortie : ', 'html5' => true, 'widget' => 'single_text']
            )
            ->add(
                'dateLimiteInscription',
                DateType::class,
                ['label' => 'Date limite d\'incription : ', 'html5' => true, 'widget' => 'single_text']
            )
            ->add(
                'nbInsriptionsMax',
                NumberType::class,
                ['label' => 'Nombre de places : ',
                    'attr' => ['placeholder' => 'Entrez un nombre de places ...']
                ]
            )
            ->add(
                'duree',
                NumberType::class,
                ['label' => 'Durée : ',
                    'attr' => ['placeholder' => 'Entrez une durée ...']
                ]
            )
            ->add(
                'infosSortie',
                TextareaType::class,
                ['label' => 'Description et infos : ',
                    'attr' => ['placeholder' => 'Entrez une description ...']
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
                    'label' => 'Campus : ']
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
                    'label' => 'Ville : ']
            )
            ->add(
                'lieu',
                EntityType::class,
                ['class' => Lieu::class,
                    'choice_label' => 'nom',
                    'label' => 'Lieu : ',
                    'placeholder' => 'Choisissez un lieu ...',
                    'choices' => []]
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
            'class' => Lieu::class
        ));
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $sortie = $event->getData();
        $lieu = $sortie->getLieu();

        if($lieu){
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
                        'label' => 'Ville : ']
                );

                $form->add('lieu', EntityType::class, array(
                    'required' => true,
                    'choice_label' => 'nom',
                    'data' => $lieu,
                    'choices' => $lieux,
                    'placeholder' => 'Choisissez un lieu ...',
                    'class' => Lieu::class
                ));
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'appbundle_sortie';
    }
}
