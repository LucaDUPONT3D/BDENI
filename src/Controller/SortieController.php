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
    #[Route('/', name: 'show_all')]
    public function showAll(
        SortieRepository $sortieRepository,
        Request $request,
        EtatSortieManager $etatSortieManager): Response
    {
        //Checker les etats
        $sorties = $sortieRepository->findAlltoCheck();
        $sorties = $etatSortieManager->checkEtatSorties($sorties);

        $model = new Model();
        $formFiltre = $this->createForm(FiltreType::class, $model);
        $formFiltre->handleRequest($request);

        if ($formFiltre->isSubmitted() && $formFiltre->isValid()) {

            $user = $this->getUser()->getId();
            $sorties = $sortieRepository->findAlltoDisplayFilter($model, $user);

        }else {

            $sorties = $sortieRepository->findAlltoDisplay();

        }
        return $this->render('sortie/showAll.html.twig', [
            'sorties' => $sorties,
            'filtreForm' => $formFiltre->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show_one', requirements:['id' => '\d+'])]
    public function show(Sortie $id, EtatSortieManager $etatSortieManager): Response
    {
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

            if ($request->request->get('submit') == 1) {
                $sortie->setEtat($etatRepository->find(1));
            }elseif ($request->request->get('submit') == 2) {
                $sortie->setEtat($etatRepository->find(2));
            }

            $sortieRepository->save($sortie, true);

            return $this->redirectToRoute('sortie_show_one', ['id' => $sortie->getId()]);
        }

        return $this->render(
            'sortie/add.html.twig',
            ['sortieForm'=> $sortieForm->createView(), 'sortie'=>$sortie]
        );
    }

    #[Route('/update/{id}', name: 'update', requirements:['id' => '\d+'])]
    public function update(SortieRepository $sortieRepository, Request $request, Sortie $id): Response
    {
        $sortieForm = $this->createForm(SortieType::class, $id);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $sortieRepository->save($id, true);

            return $this->redirectToRoute('sortie_show_one', ['id' => $id->getId()]);
        }

        return $this->render('sortie/update.html.twig', ['sortieForm'=> $sortieForm->createView(),'sortie'=>$id]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements:['id' => '\d+'])]
    public function delete(SortieRepository $sortieRepository, Request $request, Sortie $id): Response
    {
        if ($id) {
            $sortieRepository->remove($id, true);
        } else {
            throw  $this->createNotFoundException("Oops ! Delete not found !");
        }

        return $this->redirectToRoute('main_home');
    }

    #[Route('/cancel/{id}', name: 'cancel', requirements:['id' => '\d+'])]
    public function cancel(
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository,
        Request $request,
        Sortie $id
    ): Response
    {
        $cancelForm = $this->createForm(CancelType::class, $id);

        $cancelForm->handleRequest($request);

        if ($id->getOrganisateur() === $this->getUser() || $this->getUser()->getRoles()[0] == 'ROLE_ADMIN') {

            if ($cancelForm->isSubmitted() && $cancelForm->isValid()) {
                $id->setEtat($etatRepository->find(6));

                $sortieRepository->save($id, true);

                return $this->redirectToRoute('main_home');

            }
            return $this->render('sortie/cancel.html.twig', ['cancelForm'=> $cancelForm->createView(),'sortie'=>$id]);

        }else {
            return $this->redirectToRoute('main_home');
        }
    }

    #[Route('/publish/{id}', name: 'publish', requirements:['id' => '\d+'])]
    public function publish(SortieRepository $sortieRepository, EtatRepository $etatRepository, Sortie $id): Response
    {

        $id->setEtat($etatRepository->find(2));

        $sortieRepository->save($id, true);

        return $this->render('sortie/show.html.twig', ['sortie'=>$id]);
    }

    #[Route('/subscribe/{id}', name: 'subscribe', requirements:['id' => '\d+'])]
    public function subscribe(SortieRepository $sortieRepository, Sortie $id): Response
    {
        $sortie = $sortieRepository->find($id);
        if ($sortie->getEtat()->getLibelle()=='Ouverte') {

            $sortie->addParticipant($this->getUser());
            $sortieRepository->save($sortie, true);


            $resultat = $this->render('sortie/show.html.twig', ['sortie' => $sortie]);

        }else {

            $resultat = $this->redirectToRoute('main_home');
        }
        return $resultat;
    }

    #[Route('/unsubscride/{id}', name: 'unsubscride', requirements:['id' => '\d+'])]
    public function unsubscride(SortieRepository $sortieRepository, Sortie $id): Response
    {
        $sortie = $sortieRepository->find($id);
    if ($sortie->getEtat()->getLibelle()=='Ouverte' || $sortie->getEtat()->getLibelle()=='Clôturée'){
        $sortie->removeParticipant($this->getUser());

        $sortieRepository->save($sortie, true);
        $resultat = $this->render('sortie/show.html.twig', ['sortie' => $sortie]);
    }else {
        $resultat = $this->redirectToRoute('main_home');
    }
        return $resultat;
    }
}
