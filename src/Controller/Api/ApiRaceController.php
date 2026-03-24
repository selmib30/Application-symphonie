<?php

namespace App\Controller\Api;

use App\Entity\Race;
use App\Repository\RaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ApiRaceController extends AbstractController
{
    // Liste toutes les races
    #[Route('/api/v1/races', name: 'api_races_index', methods: ['GET'])]
    public function index(RaceRepository $raceRepository): JsonResponse
    {
        return $this->json(
            $raceRepository->findAll(),
            context: ['groups' => 'race:read']
        );
    }

    // Détail d'une race
    #[Route('/api/v1/races/{id}', name: 'api_races_show', methods: ['GET'])]
    public function show(Race $race): JsonResponse
    {
        return $this->json(
            $race,
            context: ['groups' => 'race:read']
        );
    }
}