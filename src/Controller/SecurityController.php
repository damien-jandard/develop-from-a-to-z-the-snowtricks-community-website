<?php

namespace App\Controller;

use App\Form\ForgotPasswordRequestFormType;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/forgot-password', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, JWTService $jwt, SendMailService $mail): Response
    {
        $form = $this->createForm(ForgotPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy(['email' => $form->get('email')->getData()]);
            
            if ($user) {    
                $token = $jwt->generate($user->getEmail(), $this->getParameter('app.jwtsecret'));
                
                $user->setToken($token);
    
                $entityManager->persist($user);
                $entityManager->flush();

                $mail->send('noreply@snowtricks.com', $user->getEmail(), 'Réinitialiser le mot de passe de votre compte Snowtricks', 'reset_password', compact('user', 'token'));
            }

            $this->addFlash('info', "Si ce compte existe, vous recevrez un email pour réinitialiser votre mot de passe.");
        }
        
        return $this->render('security/forgot_password_request.html.twig', [
            'forgotPasswordForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/reset-password/{token}', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(string $token, JWTService $jwt, UserRepository $userRepository, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {
            $payload = $jwt->getPayLoad($token);
            
            $user = $userRepository->findOneBy(['email' => $payload['user_email']]);

            if ($user && $token === $user->getToken()) {
                $form = $this->createForm(ResetPasswordFormType::class);
                $form->handleRequest($request);
                
                if ($form->isSubmitted() && $form->isValid()) {
                    $user->setPassword($userPasswordHasher->hashPassword($user,$form->get('resetPassword')->getData()))
                        ->setToken(null);
    
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success', 'Votre mot de passe a été réinitialisé.');
                    return $this->redirectToRoute('app_login');
                }
                
                return $this->render('security/reset_password.html.twig', [
                    'resetPasswordForm' => $form->createView(),
                ]);
            }
        }

        $this->addFlash('danger', 'Le token est invalide ou a expiré.');
        return $this->redirectToRoute('app_home');
    }
}
