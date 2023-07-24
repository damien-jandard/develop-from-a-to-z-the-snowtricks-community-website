<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Form\Form;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;

class DashboardHandler implements DashboardHandlerInterface
{
    public function __construct(
        private readonly UploadService $uploadService,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(
        Form $form,
        User $user
    ): void {
        $newPassword = $form->get('newPassword')->getData();
        if ($newPassword) {
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $form->get('newPassword')->getData()
                )
            );
        }

        $picture = $form->get('picture')->getData();
        if ($picture) {
            $user->setPicture($this->uploadService->upload($picture));
        }

        $this->entityManager->flush();
    }
}
