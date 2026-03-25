<?php

namespace App\Controller;

use App\Entity\Party;
use App\Entity\Character;
use App\Repository\PartyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/party')]
class PartyController extends AbstractController
{
    // 1. LISTE DES GROUPES (Modifié pour JSON)
    #[Route('', name: 'party_index', methods: ['GET'])]
    public function index(Request $request, PartyRepository $partyRepository): JsonResponse
    {
        $filter = $request->query->get('filter');
        $parties = $partyRepository->findWithFilter($filter);

        // On retourne du JSON au lieu de render()
        return $this->json($parties, 200, [], ['groups' => 'party:read']);
    }

    // 2. DÉTAIL D'UN GROUPE (Modifié pour JSON)
    #[Route('/{id}', name: 'party_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Party $party): JsonResponse
    {
        // On retourne l'objet Party directement en JSON
        return $this->json($party, 200, [], ['groups' => 'party:read']);
    }

    // 3. REJOINDRE (Modifié pour répondre en JSON à React)
    #[Route('/{id}/join/{characterId}', name: 'party_join', methods: ['POST', 'GET'])]
    #[IsGranted('ROLE_USER')]
    public function join(Party $party, Character $character, EntityManagerInterface $em): JsonResponse
    {
        if ($character->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Ce personnage ne vous appartient pas'], 403);
        }

        if ($party->getCharacters()->count() < $party->getMaxSize()) {
            $party->addCharacter($character);
            $em->flush();
            return $this->json(['message' => 'Membre ajouté'], 200);
        }

        return $this->json(['error' => 'Groupe complet'], 400);
    }

    // 4. QUITTER (Modifié pour répondre en JSON)
    #[Route('/{id}/leave/{characterId}', name: 'party_leave', methods: ['POST', 'GET'])]
    #[IsGranted('ROLE_USER')]
    public function leave(Party $party, Character $character, EntityManagerInterface $em): JsonResponse
    {
        if ($character->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Action non autorisée'], 403);
        }

        $party->removeCharacter($character);
        $em->flush();

        return $this->json(['message' => 'Membre retiré'], 200);
    }
}