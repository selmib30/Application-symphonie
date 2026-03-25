<?php

namespace App\Controller\Api;

use App\Repository\SkillRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ApiSkillController extends AbstractController
{
    // Liste toutes les compétences
    #[Route('/api/v1/skills', name: 'api_skills_index', methods: ['GET'])]
    public function index(SkillRepository $skillRepository): JsonResponse
    {
        return $this->json(
            $skillRepository->findAll(),
            context: ['groups' => 'skill:read']
        );
    }
}