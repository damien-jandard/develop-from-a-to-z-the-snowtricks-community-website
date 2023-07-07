<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Message;
use App\Form\MessageFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    #[Route('/trick/{slug}', name: 'app_trick', methods: ['GET', 'POST'])]
    public function trick(Request $request, Trick $trick, EntityManagerInterface $entityManager): Response
    {
        $message = new Message();
        $message->setUser($this->getUser());
        $message->setTrick($trick);
        $form = $this->createForm(MessageFormType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();

            $this->addFlash('success', 'Merci pour votre message.');
            return $this->redirectToRoute('app_trick', ['_fragment' => 'header', 'slug' => $trick->getSlug()]);
        }

        return $this->render('trick/trick.html.twig', [
            'trick' => $trick,
            'messageForm' => $form->createView(),
        ]);
    }
}
