<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'all')]
    public function afficher(SortieRepository $sortieRepository): Response
    {
       $sortie = $sortieRepository->findALLjoin();

        return $this->render('sortie/afficher.html.twig', [
            'sorties' => $sortie
        ]);
    }

    #[Route('/add', name: 'update')]
    public function update(SortieRepository $userRepository, Request $request): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        return $this->render('sortie/add.html.twig', ['sortieForm'=> $sortieForm->createView()]);
    }
}
