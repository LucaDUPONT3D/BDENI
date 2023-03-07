<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\FiltreType;
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
    public function afficher(SortieRepository $sortieRepository, Request $request): Response
    {
        $formFiltre = $this->createForm(FiltreType::class);

        $formFiltre->handleRequest($request);

        if ($formFiltre->isSubmitted() && $formFiltre->isValid()){

            $campus =$formFiltre->get('campus')->getData();


            $recherche = $formFiltre->get('recherche')->getData();
            $entre = $formFiltre->get('entre')->getData();
            $et = $formFiltre->get('et')->getData();

            $sorties = $sortieRepository->findALLFilter($campus, $recherche, $entre, $et);

        }else {

            $sorties = $sortieRepository->findALLjoin();
        }
        return $this->render('sortie/afficher.html.twig', [
            'sorties' => $sorties,
            'form' => $formFiltre->createView()
        ]);

    }

    #[Route('/add', name: 'add')]
    public function update(SortieRepository $userRepository, Request $request): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        return $this->render('sortie/add.html.twig', ['sortieForm'=> $sortieForm->createView()]);
    }
}
