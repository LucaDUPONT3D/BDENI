<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $this->addSortie($manager, $faker);
        $this->addEtat($manager);
        $this->addCampus($manager);


        $manager->flush();
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
