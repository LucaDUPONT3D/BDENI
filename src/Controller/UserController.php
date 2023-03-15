<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\FiltreCampusVille;
use App\Form\model\ModelCampusVille;
use App\Form\UserType;
use App\Utils\Uploader;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class UserController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, UserRepository $userRepository): Response
    {

        if ($this->getUser()) {

            $this->addFlash("success", "Bienvenue !");

            return $this->redirectToRoute('main_home');
        }


        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $user= $this->getUser();



        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);

    }

    #[Route(path: '/user/show/{id}', name: 'user_show', requirements: ['id' => '\d+'])]
    public function show(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneToDisplay($id);

        if (!$user) {
            //lance une erreur 404 si le user n'existe pas
            throw $this->createNotFoundException("Cet utilisateur n'existe pas");
        }
        return $this->render('user/show.html.twig', [
            'user' => $user
        ]);
    }
    #[Route(path: '/user/update', name: 'user_update')]
    public function update(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        Uploader $uploader
    ) : Response
    {

        $user = $this->getUser();

        $this->denyAccessUnlessGranted('user_update', $user);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            //upload photo
            /**
             * @var UploadedFile $file
             */
            $file = $form->get('image')->getData();
            //appel de l'uploader

            if ($file) {
                $newFileName = $uploader->upload(
                    $file,
                    $this->getParameter('upload_utilisateur_photo'),
                    $user->getNom());
                $user->setImage($newFileName);
            }
            $password = $form->get('plainPassword')->getData();

            if ($password) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $password
                    )
                );
            }
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("primary", "Votre profil a été modifié !");
            return $this->redirectToRoute('main_home');
        }
        return $this->render('user/update.html.twig', [
            'userForm' => $form->createView(), 'user' => $user
        ]);
    }


    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
    }

    #[Route(path: '/admin/user/{page}', name: 'admin_user_show_all')]
    public function showAll(UserRepository $userRepository, Request $request, int $page = 1)
    {

        $recherche = new ModelCampusVille();
        $rechercheFormulaire = $this->createForm(FiltreCampusVille::class, $recherche);
        $rechercheFormulaire->handleRequest($request);

        if ($rechercheFormulaire->isSubmitted() && $rechercheFormulaire->isValid()) {

            $nbSortieMax = count($userRepository->findAllToCheckFilter($recherche));
            $maxPage = ceil($nbSortieMax / UserRepository::USER_LIMIT);

            $listUser = $userRepository->findAllToDisplayFilter($recherche, $page);
        } else {
            $nbSortieMax = count($userRepository->findAllToCheck());
            $maxPage = ceil($nbSortieMax / UserRepository::USER_LIMIT);

            $listUser = $userRepository->findAllToDisplay($page);
        }

        return $this->render('admin/user/showAll.html.twig', [
            'listUser' => $listUser,
            'rechercheFormulaire' => $rechercheFormulaire->createView(),
            "currentPage" => $page,
            "maxPage" => $maxPage
        ]);
    }

    #[Route(path: '/admin/user/delete/{id}', name: 'admin_user_delete', requirements: ["id" => "\d+"])]
    public function delete(UserRepository $userRepository, Request $request, int $id,)
    {

        $utilisateurASuprimer = $userRepository->find($id);
        if($utilisateurASuprimer){
            $userRepository->remove($utilisateurASuprimer, true);
            $this->addFlash('danger', 'Utilisateur supprimé');
        }


        return $this->redirectToRoute('admin_user_show_all');

    }
    #[Route('/admin/user/ban/{id}', name: 'admin_user_ban',requirements:['id' => '\d+'])]
    public function ban(EntityManagerInterface $entityManager,UserRepository $userRepository, int $id): Response
    {
        $user = $userRepository->find($id);
        if($user){
            $user->setActif(false);
            $userRepository->save($user,true);
            $this->addFlash('primary', 'Utilisateur banni');
        }


        return $this->redirectToRoute('admin_user_show_all');
    }
    #[Route('/admin/user/unban/{id}', name: 'admin_user_unban',requirements:['id' => '\d+'])]
    public function unban(EntityManagerInterface $entityManager,UserRepository $userRepository, int $id): Response
    {
        $user = $userRepository->find($id);
        if($user){
            $user->setActif(true);
            $userRepository->save($user,true);
            $this->addFlash('primary', 'Utilisateur débanni');
        }


        return $this->redirectToRoute('admin_user_show_all');
    }

}
