<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Form\Form;

interface DashboardHandlerInterface
{
    public function __invoke(Form $form, User $user): void;
}
