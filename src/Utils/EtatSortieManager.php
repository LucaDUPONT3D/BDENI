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

    public function checkEtatSortie(array $sorties): array
    {
        $today = new \DateTime('now');

        foreach ($sorties as $sortie) {

            $dateHeureDebut = $sortie->getDateHeureDebut();
            $dateInscription = $sortie->getDateLimiteInscription();
            $nbParticipants = $sortie->getParticipants()->count();
            $nbInscriptionMax = $sortie->getNbInsriptionsMax();
            $dateHeureFin = date_add($sortie->getDateHeureDebut(),
                DateInterval::createFromDateString($sortie->getDuree() . 'minutes'));
            $dateHistory = date_add($sortie->getDateHeureDebut(),
                DateInterval::createFromDateString($sortie->getDuree() + 43200 . 'minutes'));

            if ($sortie instanceof Sortie &&
                $sortie->getEtat()->getLibelle() != 'Créée' &&
                $sortie->getEtat()->getLibelle() != 'Annulée') {
                if ($dateHeureDebut > $today &&
                    $dateInscription > $today &&
                    $nbParticipants < $nbInscriptionMax) {
                    $this->setEtatSortie($sortie, 2);
                } elseif (($dateHeureDebut > $today &&
                        $dateInscription <= $today) ||
                    $nbParticipants >= $nbInscriptionMax) {
                    $this->setEtatSortie($sortie, 3);
                } elseif ($dateHeureDebut <= $today &&
                    $dateHeureFin >= $today) {
                    $this->setEtatSortie($sortie, 4);
                } elseif ($dateHeureFin < $today &&
                    $dateHistory > $today) {
                    $this->setEtatSortie($sortie, 5);
                } elseif ($dateHistory <= $today) {
                    $this->setEtatSortie($sortie, 7);
                }
            } elseif ($sortie instanceof Sortie) {
                if ($dateHeureFin < $today &&
                    $dateHistory > $today) {
                    $this->setEtatSortie($sortie, 5);
                } elseif ($dateHistory <= $today) {
                    $this->setEtatSortie($sortie, 7);
                }
            }
        }
        return $sorties;
    }


    private function setEtatSortie(Sortie $sortie, int $etatId)
    {
        $sortie->setEtat($this->etatRepository->find($etatId));
        $this->sortieRepository->save($sortie, true);
    }

}
