<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\User;
use App\Entity\Ville;
use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Sortie;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private VilleRepository $villeRepository,
        private CampusRepository $campusRepository,
        private UserRepository $userRepository,
        private EtatRepository $etatRepository,
        private LieuRepository $lieuRepository
    )
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        //$this->addVilles(20);
        //$this->addLieux(50);
        //$this->addEtat();
        //$this->addCampus();
        $this->addUsers(3);
        //$this->addSortie(60);
    }

    private function addVilles(int $number = 10)
    {
        for ($i = 0; $i < $number; $i++) {
            $ville = new Ville();
            $ville
                ->setNom($this->faker->city)
                ->setCodePostal($this->faker->postcode);

            $this->entityManager->persist($ville);
        }
        $this->entityManager->flush();
    }

    private function addLieux(int $number = 10)
    {
        for ($i = 0; $i < $number; $i++) {
            $lieu = new Lieu();
            $lieu
                ->setNom(implode($this->faker->words(3)))
                ->setRue($this->faker->address)
                ->setLatitude($this->faker->latitude)
                ->setLongitude($this->faker->longitude)
                ->setVille($this->villeRepository->find(rand(1, 20)));
            $this->entityManager->persist($lieu);
        }
        $this->entityManager->flush();
    }

    public function addEtat()
    {
        $libelles = ['Créée','Ouverte','Clôturée','Activité en cours','Passée','Annulée','Historisée'];
        foreach ($libelles as $libelle) {
            $etat = new Etat();
            $etat->setLibelle($libelle);
            $this->entityManager->persist($etat);
        }

        $this->entityManager->flush();
    }

    public function addCampus()
    {
        $campus = ['Rennes','Quimper','Nantes','Niort','Angers'];
        foreach ($campus as $nom) {
            $camp = new Campus();
            $camp->setNom($nom);
            $this->entityManager->persist($camp);
        }

        $this->entityManager->flush();
    }

    private function addUsers(int $number = 10)
    {

//        $adminTest = new User();
//        $adminTest->setNom('Admin')
//            ->setPrenom('Admin')
//            ->setPseudo('Admin')
//            ->setRoles(['ROLE_ADMIN'])
//            ->setTelephone('0203040506')
//            ->setEmail('admin@admin.fr')
//            ->setActif(true)
//            ->setImage('image.png')
//            ->setCampus($this->campusRepository->find(rand(1, 5)));
//
//        $password = $this->passwordHasher->hashPassword($adminTest, 'admin');
//        $adminTest->setPassword($password);
//        $this->entityManager->persist($adminTest);
//
//        $userTest = new User();
//        $userTest->setNom('User')
//            ->setPrenom('User')
//            ->setPseudo('User')
//            ->setRoles(['ROLE_USER'])
//            ->setTelephone('0102030405')
//            ->setEmail('user@user.fr')
//            ->setActif(true)
//            ->setImage('image.png')
//            ->setCampus($this->campusRepository->find(rand(1, 5)));
//
//        $password = $this->passwordHasher->hashPassword($userTest, 'user');
//        $userTest->setPassword($password);
//        $this->entityManager->persist($userTest);

        for ($i = 0; $i < $number; $i++) {
            $user = new User();
            $user->setNom($this->faker->lastName)
                ->setPrenom($this->faker->firstName)
                ->setPseudo($this->faker->userName)
                ->setRoles(['ROLE_USER'])
                ->setTelephone($this->faker->phoneNumber)
                ->setEmail($this->faker->email)
                ->setActif($this->faker->boolean())
                ->setImage('image.png')
                ->setCampus($this->campusRepository->find(rand(1, 5)));

                $password = $this->passwordHasher->hashPassword($user, '123456');
                $user->setPassword($password);

            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();
    }

    public function addSortie(int $number)
    {
        for ($i=0; $i< $number; $i++) {
            $sortie = new Sortie();
            $sortie->setNom(implode($this->faker->words(3)));
            $sortie->setDateHeureDebut($this->faker->dateTimeBetween('-1 month', '+ 1 month'));
            $sortie->setDuree($this->faker->randomNumber(3));
            $sortie->setDateLimiteInscription($this->faker->dateTimeBetween('-1 month', $sortie->getDateHeureDebut()));
            $sortie->setNbInsriptionsMax($this->faker->randomNumber(1));
            $sortie->setInfosSortie(implode($this->faker->words(3)));
            $sortie->setEtat($this->etatRepository->find(rand(1, 2)));
            $sortie->setCampus($this->campusRepository->find(rand(1, 5)));
            $sortie->setOrganisateur($this->userRepository->find(rand(1, 10)));
            $sortie->setLieu($this->lieuRepository->find(rand(1, 50)));
            $sortie->addParticipant($sortie->getOrganisateur());

            $this->entityManager->persist($sortie);
        }
        $this->entityManager->flush();
    }

}
