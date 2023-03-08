<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Utils\Uploader;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

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

    #[Route(path: '/user/{id}', name: 'user_show', requirements: ['id' => '\d+'])]
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

    #[Route(path: '/user/update', name: 'user_update')]
    public function update(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Uploader $uploader): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        dump($form->isSubmitted() && $form->isValid());
        if ($form->isSubmitted() && $form->isValid()) {
            dump('bip');
            //upload photo
            /**
             * @var UploadedFile $file
             */
            $file = $form->get('image')->getData();
            //appel de l'uploader
            $newFileName = $uploader->upload(
                $file,
                $this->getParameter('upload_utilisateur_photo'),
                $user->getNom());
            $user->setImage($newFileName);


            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success", "Modifications effectués");
            return $this->redirectToRoute('sortie_all');
        }
        return $this->render('user/update.html.twig', [
            'registrationForm' => $form->createView(), 'user' => $user
        ]);
    }


    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {

    }
}
