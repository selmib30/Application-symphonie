<?php

namespace App\Controller\Api;

use App\Entity\Character;
use App\Repository\CharacterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ApiCharacterController extends AbstractController
{
    // Liste tous les personnages (filtrable par name, class, race)
    #[Route('/api/v1/characters', name: 'api_characters_index', methods: ['GET'])]
    public function index(Request $request, CharacterRepository $repository): JsonResponse
    {
        $name = $request->query->get('name');
        $classId = $request->query->get('class') ? (int)$request->query->get('class') : null;
        $raceId  = $request->query->get('race')  ? (int)$request->query->get('race')  : null;

        $characters = $repository->findWithFilters($name, $classId, $raceId);

        return $this->json(
            $characters,
            context: ['groups' => 'character:read']
        );
    }

    // Détail complet d’un personnage
    #[Route('/api/v1/characters/{id}', name: 'api_characters_show', methods: ['GET'])]
    public function show(Character $character): JsonResponse
    {
        return $this->json(
            $character,
            context: ['groups' => 'character:read']
        );
    }
}