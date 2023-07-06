<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function dashboard(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UploadService $upload): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('newPassword')->getData()) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('newPassword')->getData()
                    )
                );
            }
            if ($form->get('picture')->getData()) {
                $file = $form->get('picture')->getData();
                $user->setPicture($upload->upload($file));
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a été mis à jour.');
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('dashboard/dashboard.html.twig', [
            'userForm' => $form->createView(),
            'user' => $user
        ]);
    }
}
