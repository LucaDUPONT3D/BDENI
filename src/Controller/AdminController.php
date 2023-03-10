<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\FiltreType;
use App\Form\model\Model;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/list', name: 'user')]
    public function list(UserRepository $userRepository,Request $request,EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager -> getRepository(User::class);
        $users = $userRepository->createQueryBuilder('u')
            ->orderBy('u.nom', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/showAll.html.twig',[
            'users' => $users,

    ]);
    }

    #[Route('/ban/{id}', name: 'ban',requirements:['id' => '\d+'])]
    public function ban(User $user,EntityManagerInterface $entityManager,UserRepository $userRepository): Response
    {
    if ($user->isActif() ){
        $this->addFlash('danger', "Impossible de bannir un admin");
    }
        $userRepository = $entityManager -> getRepository(User::class);
        $user->setActif(!$user->isActif());
        $userRepository->save($user,true);

        return $this->render('admin/showAll.html.twig');
    }
//    #[Route('/loadCSV', name: 'load_csv')]
//    public function loadCSV(): Response
//    {
//
//        return $this->render('');
//    }
//    #[Route('/{id}/delete', name: 'delete')]
//    public function delete(): Response
//    {
//
//        return $this->render('');
//    }
}
