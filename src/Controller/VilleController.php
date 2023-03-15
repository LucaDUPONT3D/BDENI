<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\FiltreCampusVille;
use App\Form\model\ModelCampusVille;
use App\Form\VilleType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/ville', name: 'admin_ville_')]
class VilleController extends AbstractController
{
    #[Route('/add/{page}', name: 'add')]
    public function add(Request $request, VilleRepository $villeRepository, int $page = 1): Response
    {

        $model = new ModelCampusVille();

        $filtreVilleForm = $this->createForm(FiltreCampusVille::class, $model);
        $filtreVilleForm->handleRequest($request);



        if ($filtreVilleForm->isSubmitted() && $filtreVilleForm->isValid()) {

            $nbSortieMax = count($villeRepository->findAllToCheckFilter($model));
            $maxPage = ceil($nbSortieMax / VilleRepository::VILLE_LIMIT);

            $listeVille = $villeRepository->findAllToDisplayFilter($model, $page);

        }else {

            $nbSortieMax = count($villeRepository->findAllToCheck());
            $maxPage = ceil($nbSortieMax / VilleRepository::VILLE_LIMIT);

            $listeVille = $villeRepository->findAllToDisplay($page);
        }

        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);


        if ($villeForm->isSubmitted() && $villeForm->isValid()) {

            $villeRepository->save($ville, true);

            $this->addFlash('primary', 'Ville créée');

            return $this->redirectToRoute('admin_ville_add');
        }



        return $this->render('admin/ville/add.html.twig', [
            'tableauVille' => $listeVille,
            'filtreCampusVilleForm' => $filtreVilleForm->createView(),
            'villeForm' => $villeForm->createView(),
            "currentPage" => $page,
            "maxPage" => $maxPage

        ]);

    }

    #[Route('/delete/{id}', name: 'delete', requirements: ["id" => "\d+"])]
    public function delete(VilleRepository $villeRepository, int $id, LieuRepository $lieuRepository): Response
    {

        $villeasuprimer = $villeRepository->find($id);
        $reussi= $lieuRepository->findBy(['ville' => $villeasuprimer]);

        if ($reussi) {
            $this->addFlash('warning', 'Suppression impossible, ville en cours d\'utilisation');
        } else {
            $villeRepository->remove($villeasuprimer, true);
            $this->addFlash('danger', 'Ville supprimée');
        }


        return $this->redirectToRoute('admin_ville_add');
    }
    #[Route('/update/{id}', name: 'update', requirements: ["id" => "\d+"])]
    public function update(Request $request, VilleRepository $villeRepository, int $id): Response
    {

        $ville = $villeRepository->find($id);

        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);

        if ($villeForm->isSubmitted() && $villeForm->isValid()) {
            $villeRepository->save($ville, true);
            $this->addFlash('primary', 'Ville modifiée');
            return  $this->redirectToRoute('admin_ville_add');
        }


        return $this->render('admin/ville/update.html.twig', [
            'villeForm' => $villeForm->createView()
        ]);

    }
}
