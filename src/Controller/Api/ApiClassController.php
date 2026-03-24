<?php

namespace App\Controller\Api;

use App\Entity\CharacterClass;
use App\Repository\CharacterClassRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ApiClassController extends AbstractController
{
    // Liste toutes les classes avec leurs compétences
    #[Route('/api/v1/classes', name: 'api_classes_index', methods: ['GET'])]
    public function index(CharacterClassRepository $repository): JsonResponse
    {
        return $this->json(
            $repository->findAll(),
            context: ['groups' => 'class:read']
        );
    }

    // Détail d'une classe avec ses compétences
    #[Route('/api/v1/classes/{id}', name: 'api_classes_show', methods: ['GET'])]
    public function show(CharacterClass $characterClass): JsonResponse
    {
        return $this->json(
            $characterClass,
            context: ['groups' => 'class:read']
        );
    }
}