<?php

namespace App\Controller\Api;

use App\Form\CancelType;
use App\Form\FiltreType;
use App\Entity\Sortie;
use App\Form\model\Model;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Utils\EtatSortieManager;
use DateInterval;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/sortie', name: 'api_sortie_')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'show_all')]
    public function showAll(SortieRepository  $sortieRepository, EtatSortieManager $etatSortieManager): Response
    {

        $sorties = $sortieRepository->findAllToCheck();
        $sorties = $etatSortieManager->checkEtatSorties($sorties);

        return $this->json($sorties, 200, [], ['groups'=>'api_sortie_show_all']);
    }

    #[Route('/{id}', name: 'show_one', requirements: ['id' => '\d+'])]
    public function show(int $id, EtatSortieManager $etatSortieManager, SortieRepository $sortieRepository): Response
    {

        $sortie = $sortieRepository->findOneToDisplay($id);
        $sortie = $etatSortieManager->checkEtatSortie($sortie);

        return $this->json($sortie, 200, [], ['groups'=>'api_sortie_show_one']);
    }
}
