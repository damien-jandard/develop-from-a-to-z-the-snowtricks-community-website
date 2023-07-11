<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Message;
use App\Form\MessageFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/trick', name: 'app_trick_')]
class TrickController extends AbstractController
{
    #[Route('/{slug}', name: 'show', methods: ['GET', 'POST'])]
    public function show(#[CurrentUser] User $user, Request $request, Trick $trick, EntityManagerInterface $entityManager): Response
    {
        $message = new Message();
        $message->setUser($user);
        $message->setTrick($trick);
        $form = $this->createForm(MessageFormType::class, $message)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();

            $this->addFlash('success', 'Merci pour votre message.');
            return $this->redirectToRoute('app_trick_show', ['_fragment' => 'header', 'slug' => $trick->getSlug()]);
        }

        return $this->render('trick/trick.html.twig', [
            'trick' => $trick,
            'messageForm' => $form,
        ]);
    }
}
