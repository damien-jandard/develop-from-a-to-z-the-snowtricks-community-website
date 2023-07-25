<?php

namespace App\Controller;

use App\Form\ContactFormType;
use App\Repository\TrickRepository;
use App\Service\SendMailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function home(
        Request $request,
        TrickRepository $trickRepository,
        SendMailService $mail
    ): Response {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $trickRepository->getTrickPaginator($offset);

        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            $mail->send(
                'noreply@snowtricks.com',
                'contact@snowtricks.com',
                'Contact Snowtricks',
                'contact',
                compact('contact')
            );

            $this->addFlash(
                'success',
                'Votre message a été envoyé avec succès, nous vous répondrons dans les plus brefs délais.'
            );
            return $this->redirectToRoute('app_home', ['_fragment' => 'header']);
        }

        return $this->render('home/home.html.twig', [
            'contactForm' => $form->createView(),
            'tricks' => $paginator,
            'previous' => $offset - TrickRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + TrickRepository::PAGINATOR_PER_PAGE)
        ]);
    }
}
