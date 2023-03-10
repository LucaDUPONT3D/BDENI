<?php

namespace App\Utils;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateInterval;

class EtatSortieManager
{

    public function __construct(private EtatRepository $etatRepository, private SortieRepository $sortieRepository)
    {
    }

    public function checkEtatSortie(Sortie $sortie): Sortie
    {
        $this->check($sortie);

        return $sortie;
    }

    public function checkEtatSorties(array $sorties): array
    {

        foreach ($sorties as $sortie) {

            $this->check($sortie);
        }
        return $sorties;
    }


    private function check(Sortie $sortie)
    {

        $today = new \DateTime('now');
        $dateHeureDebut = clone $sortie->getDateHeureDebut();
        $dateHeureDebut2 = clone $sortie->getDateHeureDebut();
        $dateInscription = $sortie->getDateLimiteInscription();
        $nbParticipants = $sortie->getParticipants()->count();
        $nbInscriptionMax = $sortie->getNbInsriptionsMax();
        $dateHeureFin = date_add($dateHeureDebut,
            DateInterval::createFromDateString($sortie->getDuree() . 'minutes'));
        $dateHistory = date_add($dateHeureDebut2,
            DateInterval::createFromDateString($sortie->getDuree() + 43200 . 'minutes'));

        if ($sortie->getEtat()->getLibelle() != 'Créée' &&
            $sortie->getEtat()->getLibelle() != 'Annulée') {
            if ($sortie->getDateHeureDebut() > $today &&
                $dateInscription > $today &&
                $nbParticipants < $nbInscriptionMax) {
                $this->setEtatSortie($sortie, 2);
            } elseif ($sortie->getDateHeureDebut() > $today &&
                ($dateInscription < $today ||
                $nbParticipants >= $nbInscriptionMax)) {
                $this->setEtatSortie($sortie, 3);
            } elseif ($sortie->getDateHeureDebut() < $today &&
                $dateHeureFin > $today) {
                $this->setEtatSortie($sortie, 4);
            } elseif ($dateHeureFin < $today &&
                $dateHistory > $today) {
                $this->setEtatSortie($sortie, 5);
            } elseif ($dateHistory < $today) {
                $this->setEtatSortie($sortie, 7);
            }
        } else {
            if ($dateHeureFin < $today &&
                $dateHistory > $today) {
                $this->setEtatSortie($sortie, 5);
            } elseif ($dateHistory <= $today) {
                $this->setEtatSortie($sortie, 7);
            }
        }
    }

    private function setEtatSortie(Sortie $sortie, int $etatId)
    {
        $sortie->setEtat($this->etatRepository->find($etatId));
        $this->sortieRepository->save($sortie, true);
    }

}
