<?php

namespace App\Controller\Api;


use App\Entity\Lieu;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/lieu', name: 'api_lieu_')]
class LieuController extends AbstractController
{
    #[Route('/ville/{id}', name: 'show_by_ville')]
    public function showSearch(Request $request, LieuRepository $lieuRepository, Ville $id): Response
    {

        $lieux = $lieuRepository->findBy(['ville'=> $id], ["nom" => 'DESC']);

        return $this->json($lieux, 200, [], ['groups'=>'api_lieu_show_ville']);
    }

    #[Route('/{id}', name: 'show_one', requirements:['id' => '\d+'])]
    public function show(Request $request, LieuRepository $lieuRepository, Lieu $id): Response
    {

        return $this->json($id, 200, [], ['groups'=>'api_lieu_show_one']);
    }
}
