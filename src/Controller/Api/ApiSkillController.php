<?php

namespace App\Controller\Api;

use App\Repository\SkillRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Attribute\Groups;

class ApiSkillController extends AbstractController
{
    // Liste toutes les compétences
    #[Route('/api/v1/skills', name: 'api_skills_index', methods: ['GET'])]
    public function index(SkillRepository $skillRepository): JsonResponse
    {
        $skills = $skillRepository->findAll();

        return $this->json(
            $skills,
            200, // Code de statut explicite
            [],  // Headers
            ['groups' => 'skill:read']
        );
    }
}
