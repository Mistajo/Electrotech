<?php

namespace App\Controller\User\Profile;

use App\Form\EditNameFormType;
use App\Form\EditEmailFormType;
use App\Form\EditPasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'user.profile.index')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('pages/user/profile/index.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/profile-edit-name', name: 'user.profile-edit-name.index', methods: [
        'GET', 'PUT'
    ])]
    public function editName(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditNameFormType::class, $user, ['method' => 'PUT']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Votre prenom et votre nom ont bien été modifiés');
            return $this->redirectToRoute('user.profile.index');
        }
        return $this->render('pages/user/profile/edit_name.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile-edit-email', name: 'user.profile-edit-email.index', methods: [
        'GET', 'PUT'
    ])]
    public function editEmail(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditEmailFormType::class, $user, ['method' => 'PUT']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Votre email a bien été modifié');
            return $this->redirectToRoute('user.profile.index');
        }
        return $this->render('pages/user/profile/edit_email.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile-edit-password', name: 'user.profile-edit-password.index', methods: [
        'GET', 'PUT'
    ])]
    public function editPassword(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditPasswordFormType::class, null, ['method' => 'PUT']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all();
            $password = $data['edit_password_form']['password']['first'];
            $passwordHashed = $hasher->hashPassword($user, $password);
            $user->setPassword($passwordHashed);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Votre mot de passe a bien été modifié');
            return $this->redirectToRoute('user.profile.index');
        }
        return $this->render('pages/user/profile/edit_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/delete', name: 'user.profile.delete', methods: [
        'DELETE'
    ])]
    public function delete(Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid("profile-delete", $request->request->get('csrf_token'))) {
            $user = $this->getUser();

            $em->remove($user);
            $em->flush();

            $this->container->get('security.token_storage')->setToken(null);

            $this->addFlash('success', "Votre compte a bien été supprimé. Vous allez nous manquer.");
        }

        return $this->redirectToRoute('visitor.authentication.login');
    }
}
