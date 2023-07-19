<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Message;
use App\Form\MessageFormType;
use App\Form\TrickFormType;
use App\Repository\TrickRepository;
use App\Service\UploadService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/trick', name: 'app_trick_')]
class TrickController extends AbstractController
{
    #[Route('/delete/{slug}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Trick $trick, TrickRepository $trickRepository, UploadService $uploadService): Response
    {
        if ($this->isCsrfTokenValid(sprintf('delete%s', $trick->getSlug()), $request->request->get('_token'))) {
            $uploadService->removePictures($trick);
            $trickRepository->remove($trick, true);
            $this->addFlash('success', 'La figure a été supprimée.');
        }
        
        return $this->redirectToRoute('app_home');
    }

    #[Route('/edit/{slug}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(#[CurrentUser] User $user, Request $request, Trick $trick, UploadService $uploadService, TrickRepository $trickRepository): Response
    {        
        $form = $this->createForm(TrickFormType::class, $trick, ['validation_groups' => 'edit'])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setUpdatedAt(new DateTimeImmutable());
            $uploadService->uploadPictures($trick);
            $uploadService->uploadVideos($trick);
            $trickRepository->save($trick, true);

            $this->addFlash('success', 'La figure a été mise à jour.');
            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
        }
        
        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'trickForm' => $form,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(#[CurrentUser] User $user, Request $request, SluggerInterface $slugger, UploadService $uploadService, TrickRepository $trickRepository): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickFormType::class, $trick, ['validation_groups' => 'new'])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setUser($user);
            $trick->setSlug($slugger->slug($trick->getName())->lower());
            $uploadService->uploadPictures($trick);
            $uploadService->uploadVideos($trick);

            $trickRepository->save($trick, true);

            $this->addFlash('success', 'Votre figure a été ajouté.');
            return $this->redirectToRoute('app_trick_show', ['_fragment' => 'header', 'slug' => $trick->getSlug()]);
        }
        
        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'trickForm' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'show', methods: ['GET', 'POST'])]
    public function show(#[CurrentUser] ?User $user, Request $request, Trick $trick, EntityManagerInterface $entityManager): Response
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
