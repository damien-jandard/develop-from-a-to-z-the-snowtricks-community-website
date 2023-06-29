<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\JWTService;
use App\Service\SendMailService;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $header = [
                'alg' => 'HS256',
                'typ' => 'JWT'
            ];

            $payload = [
                'user_email' => $user->getEmail()
            ];

            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            $user->setToken($token);

            $entityManager->persist($user);
            $entityManager->flush();
            
            $mail->send('noreply@snowtricks.com', $user->getEmail(), 'Activation de votre compte Snowtricks', 'register', compact('user', 'token'));

            $this->addFlash('info', 'Un email vous a été envoyé afin d\'activer votre compte.');
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/check/{token}', name: 'app_check_activation')]
    public function checkActivation(string $token, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {
            $payload = $jwt->getPayLoad($token);

            $user = $userRepository->findOneBy(['email' => $payload['user_email']]);

            if ($user && !$user->isIsValid() && $token === $user->getToken()) {
                $user->setIsValid(true)
                    ->setToken(null);
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Votre compte utilisateur est désormais activé.');
                return $this->redirectToRoute('app_home');
            }
        }

        $this->addFlash('danger', 'Le token est invalide ou a expiré.');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/activation', name: 'app_resend_activation')]
    public function resendActivation(JWTService $jwt, SendMailService $mail, EntityManagerInterface $entityManager): Response
    {
        /**
         * @var $user User
         */
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette fonctionnalité.');
            return $this->redirectToRoute('app_login');
        }

        if ($user->isIsValid()) {
            $this->addFlash('info', 'Vous compte est déjà activé.');
            return $this->redirectToRoute('app_home');
        }

        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        $payload = [
            'user_email' => $user->getEmail()
        ];

        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        $user->setToken($token);

        $entityManager->persist($user);
        $entityManager->flush();

        $mail->send('noreply@snowtricks.com', $user->getEmail(), 'Activation de votre compte Snowtricks', 'register', compact('user', 'token'));

        $this->addFlash('info', 'Un nouvel email vous a été envoyé afin d\'activer votre compte.');
        return $this->redirectToRoute('app_home');
    }
}
