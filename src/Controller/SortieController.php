<?php

namespace App\Controller;

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

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('/{page}', name: 'show_all',  requirements: ["page"=>"\d+"])]
    public function showAll(
        SortieRepository $sortieRepository,
        Request $request,
        EtatSortieManager $etatSortieManager,
        int $page = 1
    ) : Response
    {

        $nbSortieMax = count($sortieRepository->findAllToCheck());
        $maxPage = ceil($nbSortieMax/SortieRepository::SORTIE_LIMIT);

        //Checker les etats
        $sorties = $sortieRepository->findAllToCheck();
        $sorties = $etatSortieManager->checkEtatSorties($sorties);

        //Création du formulaire de filtre
        $model = new Model();
        $formFiltre = $this->createForm(FiltreType::class, $model);
        $formFiltre->handleRequest($request);


            if ($formFiltre->isSubmitted() && $formFiltre->isValid()) {

                $user = $this->getUser()->getId();
                $sorties = $sortieRepository->findAllToDisplayFilter($model, $user);

                return $this->render('sortie/showAll.html.twig', [
                    'sorties' => $sorties,
                    'filtreForm' => $formFiltre->createView(),
                    "currentPage"=>1,
                    "maxPage"=>1
                ]);
            }else {
                if ($page >= 1 && $page <= $maxPage) {

                $sorties = $sortieRepository->findAllToDisplay($page);

                } else {
                    throw $this->createNotFoundException("Oops ! Page non trouvée !");
                }

            }

        return $this->render('sortie/showAll.html.twig', [
            'sorties' => $sorties,
            'filtreForm' => $formFiltre->createView(),
            "currentPage"=>$page,
            "maxPage"=>$maxPage
        ]);
    }

    #[Route('/{id}', name: 'show_one', requirements:['id' => '\d+'])]
    public function show(int $id, EtatSortieManager $etatSortieManager, SortieRepository $sortieRepository): Response
    {

        $id = $sortieRepository->findOneToDisplay($id);
        $id = $etatSortieManager->checkEtatSortie($id);

        return $this->render('sortie/show.html.twig', ['sortie'=> $id]);
    }

    #[Route('/add', name: 'add')]
    public function add(SortieRepository $sortieRepository, EtatRepository $etatRepository, Request $request): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->setOrganisateur($this->getUser());
            $sortie->addParticipant($this->getUser());

            if ($request->request->get('submit') == 1) {
                $sortie->setEtat($etatRepository->find(1));
            }elseif ($request->request->get('submit') == 2) {
                $sortie->setEtat($etatRepository->find(2));
            }

            $sortieRepository->save($sortie, true);

            $this->addFlash("primary", "Sortie créée !");

            return $this->redirectToRoute('sortie_show_one', ['id' => $sortie->getId()]);
        }

        return $this->render(
            'sortie/add.html.twig',
            ['sortieForm'=> $sortieForm->createView(), 'sortie'=>$sortie]
        );
    }

    #[Route('/update/{id}', name: 'update', requirements:['id' => '\d+'])]
    public function update(SortieRepository $sortieRepository, Request $request, int $id): Response
    {
        $id = $sortieRepository->findOneToDisplay($id);

        $sortieForm = $this->createForm(SortieType::class, $id);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $sortieRepository->save($id, true);

            $this->addFlash("primary", "Sortie modifiée !");

            return $this->redirectToRoute('sortie_show_one', ['id' => $id->getId()]);
        }

        return $this->render('sortie/update.html.twig', ['sortieForm'=> $sortieForm->createView(),'sortie'=>$id]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements:['id' => '\d+'])]
    public function delete(SortieRepository $sortieRepository, Request $request, int $id): Response
    {

        $id = $sortieRepository->findOneToDisplay($id);

        if ($id) {
            $sortieRepository->remove($id, true);
        } else {
            throw  $this->createNotFoundException("Oops ! Delete not found !");
        }

        $this->addFlash("danger", "Sortie supprimée !");

        return $this->redirectToRoute('main_home');
    }

    #[Route('/cancel/{id}', name: 'cancel', requirements:['id' => '\d+'])]
    public function cancel(
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository,
        Request $request,
        int $id
    ): Response
    {

        $id = $sortieRepository->findOneToDisplay($id);

        $cancelForm = $this->createForm(CancelType::class, $id);

        $cancelForm->handleRequest($request);

        if ($id->getOrganisateur() === $this->getUser() || $this->getUser()->getRoles()[0] == 'ROLE_ADMIN') {

            if ($cancelForm->isSubmitted() && $cancelForm->isValid()) {
                $id->setEtat($etatRepository->find(6));

                $sortieRepository->save($id, true);

                $this->addFlash("warning", "Sortie annulée !");

                return $this->redirectToRoute('main_home');

            }
            return $this->render('sortie/cancel.html.twig', ['cancelForm'=> $cancelForm->createView(),'sortie'=>$id]);

        }else {
            return $this->redirectToRoute('main_home');
        }
    }

    #[Route('/publish/{id}', name: 'publish', requirements:['id' => '\d+'])]
    public function publish(SortieRepository $sortieRepository, EtatRepository $etatRepository, int $id): Response
    {
        $id = $sortieRepository->findOneToDisplay($id);

        $id->setEtat($etatRepository->find(2));

        $sortieRepository->save($id, true);

        $this->addFlash("success", "Sortie publiée !");

        return $this->render('sortie/show.html.twig', ['sortie'=>$id]);
    }

    #[Route('/subscribe/{id}', name: 'subscribe', requirements:['id' => '\d+'])]
    public function subscribe(SortieRepository $sortieRepository, int $id): Response
    {
        $id = $sortieRepository->findOneToDisplay($id);

        $sortie = $sortieRepository->find($id);
        if ($sortie->getEtat()->getLibelle()=='Ouverte') {

            $sortie->addParticipant($this->getUser());
            $sortieRepository->save($sortie, true);

            $this->addFlash("primary", "Inscription pris en compte !");


            $resultat = $this->render('sortie/show.html.twig', ['sortie' => $sortie]);

        }else {

            $resultat = $this->redirectToRoute('main_home');
        }
        return $resultat;
    }

    #[Route('/unsubscride/{id}', name: 'unsubscride', requirements:['id' => '\d+'])]
    public function unsubscride(SortieRepository $sortieRepository, int $id): Response
    {
        $id = $sortieRepository->findOneToDisplay($id);

        $sortie = $sortieRepository->find($id);
    if ($sortie->getEtat()->getLibelle()=='Ouverte' || $sortie->getEtat()->getLibelle()=='Clôturée'){
        $sortie->removeParticipant($this->getUser());

        $sortieRepository->save($sortie, true);

        $this->addFlash("danger", "Désinscription pris en compte !");

        $resultat = $this->render('sortie/show.html.twig', ['sortie' => $sortie]);
    }else {
        $resultat = $this->redirectToRoute('main_home');
    }
        return $resultat;
    }
}
