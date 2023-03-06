<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\User;
use App\Entity\Ville;
use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Sortie;
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
        private UserPasswordHasherInterface $passwordHasher
    )
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $this->addVilles(20);
        //$this->addLieux(50);
        //$this->addUsers(20);
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
                ->setNom($this->faker->title)
                ->setRue($this->faker->address)
                ->setLatitude($this->faker->latitude)
                ->setLongitude($this->faker->longitude)
                ->setVille($this->entityManager->find(Ville::class, $this->faker->numberBetween(1,20)));
            $this->entityManager->persist($lieu);
        }
        $this->entityManager->flush();
    }

    private function addUsers(int $number = 10)
    {

        $admin = new User();
        $admin->setNom('Admin')
            ->setPrenom('Admin')
            ->setPseudo('Admin')
            ->setRoles(['ROLE_ADMIN'])
            ->setTelephone('0203040506')
            ->setActif(true)
            ->setImage('image.png');

        $password = $this->passwordHasher->hashPassword($admin, 'admin');
        $admin->setPassword($password);


        for ($i = 0; $i < $number; $i++) {
            $user = new User();
            $user->setNom($this->faker->lastName)
                ->setPrenom($this->faker->firstName)
                ->setPseudo($this->faker->userName)
                ->setRoles(['ROLE_USER'])
                ->setTelephone($this->faker->phoneNumber)
                ->setActif($this->faker->boolean())
                ->setImage('image.png');

                $password = $this->passwordHasher->hashPassword($user, '123');
                $user->setPassword($password);

            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();
    }


    public function addSortie(ObjectManager $objectManager, Generator $generator){
        for ($i=0; $i<30;$i++){
            $sortie = new Sortie();
            $sortie->setNom($generator->words(2));
            $sortie->setDateHeureDebut($generator->dateTime);
            $sortie->setDuree($generator->randomNumber(1000));
            $sortie->setDateLimiteInscription($generator->dateTimeBetween($sortie->getDateHeureDebut(),'now'));
            $sortie->setNbInsriptionsMax($generator->randomNumber(100));
            $sortie->setInfosSortie($generator->words([15]));
            $objectManager->persist($sortie);
        }
        $objectManager->flush();
    }
    public function addEtat(ObjectManager $objectManager){
        $libelles = ['Créée','Ouverte','Clôturée','Activité en cours','passée','Annulée'];
        $etat = new Etat();
      foreach ($libelles as  $libelle ){
            $etat->setLibelle($libelle);
          $objectManager->persist($etat);
        }
        $objectManager->flush();
    }
    public function addCampus(ObjectManager $objectManager){
        $campus = ['Rennes','Quimper','Nantes','Niort','Angers'];
        $camp = new Campus();
        foreach ($campus as  $nom ){
            $camp->setNom($nom);
            $objectManager->persist($camp);
        }
        $objectManager->flush();
    }




}
