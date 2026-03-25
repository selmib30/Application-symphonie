<?php

namespace App\Controller\Api;

use App\Repository\PartyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ApiPartyController extends AbstractController
{
    // Liste tous les groupes (filtrable : complets / disponibles)
    #[Route('/api/v1/parties', name: 'api_parties_index', methods: ['GET'])]
    public function index(Request $request, PartyRepository $repository): JsonResponse
    {
        $filter = $request->query->get('available'); // "true" pour disponibles uniquement
        $parties = $repository->findWithAvailabilityFilter($filter);

        return $this->json(
            $parties,
            context: ['groups' => 'party:read']
        );
    }

    // Détail complet d’un groupe avec ses membres
    #[Route('/api/v1/parties/{id}', name: 'api_parties_show', methods: ['GET'])]
    public function show($id, PartyRepository $repository): JsonResponse
    {
        $party = $repository->find($id);
        if (!$party) {
            return $this->json(['error' => 'Party not found'], 404);
        }

        return $this->json(
            $party,
            context: ['groups' => 'party:read']
        );
    }
}