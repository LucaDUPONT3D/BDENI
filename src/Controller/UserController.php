<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('sortie_all');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);

    }

    #[Route(path: '/user/{id}', name: 'user_show', requirements:['id' => '\d+']) ]
    public function show(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            //lance une erreur 404 si le user n'existe pas
            throw $this->createNotFoundException("Cet utilisateur n'existe pas");
        }
        return $this->render('user/show.html.twig', [
            'user' => $user
        ]);
    }
    #[Route(path: '/user/update', name: 'user_update', requirements:['id' => '\d+']) ]
    public function update(UserRepository $userRepository): Response
    {
        $user = $userRepository->find($this->getUser());

        if (!$user) {
            //lance une erreur 404 si le user n'existe pas
            throw $this->createNotFoundException("Cet utilisateur n'existe pas");
        }
        return $this->render('user/update.html.twig', [
            'user' => $user
        ]);
    }


    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {

    }
}
