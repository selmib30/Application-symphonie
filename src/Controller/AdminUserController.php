<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route; // Changement ici (Attribute au lieu de Annotation)
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminUserController extends AbstractController
{
    #[Route('/admin/users', name: 'app_admin_user_index')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin_user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
}
