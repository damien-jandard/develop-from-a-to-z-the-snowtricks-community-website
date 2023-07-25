<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Service\DashboardHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function dashboard(
        #[CurrentUser] User $user,
        Request $request,
        DashboardHandlerInterface $dashboardHandler
    ): Response {
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dashboardHandler($form, $user);

            $this->addFlash(
                'success',
                'Votre profil a été mis à jour.'
            );
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('dashboard/dashboard.html.twig', [
            'userForm' => $form->createView(),
            'user' => $user
        ]);
    }
}
