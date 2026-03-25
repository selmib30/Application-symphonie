<?php

namespace App\Controller;

use App\Entity\Character;
use App\Form\CharacterType;
use App\Repository\CharacterRepository;
use App\Repository\CharacterClassRepository;
use App\Repository\RaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/character')]
class CharacterController extends AbstractController
{
    #[Route('', name: 'character_index', methods: ['GET'])]
    public function index(
        Request $request,
        CharacterRepository $characterRepository,
        CharacterClassRepository $classRepository,
        RaceRepository $raceRepository
    ): Response {
        // Récupération des paramètres de filtrage depuis l'URL (GET)
        $name    = $request->query->get('name');
        $classId = $request->query->get('class') ? (int) $request->query->get('class') : null;
        $raceId  = $request->query->get('race')  ? (int) $request->query->get('race')  : null;
        $availability = $request->query->get('availability'); // Filtre groupe

        // On récupère tous les personnages correspondants aux filtres (vue publique)
        $characters = $characterRepository->findWithFilters($name, $classId, $raceId, $availability);

        return $this->render('character/index.html.twig', [
            'characters' => $characters,
            'classes'    => $classRepository->findAll(),
            'races'      => $raceRepository->findAll(),
            'filters'    => [
                'name' => $name,
                'class' => $classId,
                'race' => $raceId,
                'availability' => $availability
            ],
        ]);
    }

    #[Route('/new', name: 'character_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $character = new Character();
        $character->setUser($this->getUser());

        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hp = 10 + ($character->getConstitution() - 10) + ($character->getLevel() * 5);
            $character->setHealthPoints($hp);

            $entityManager->persist($character);
            $entityManager->flush();

            return $this->redirectToRoute('character_index');
        }

        return $this->render('character/new.html.twig', [
            'character' => $character,
            'form'      => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'character_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Character $character, EntityManagerInterface $entityManager): Response
    {
        // Sécurité : Seul le propriétaire modifie son personnage
        if ($character->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Accès refusé.");
        }

        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hp = 10 + ($character->getConstitution() - 10) + ($character->getLevel() * 5);
            $character->setHealthPoints($hp);

            $entityManager->flush();
            return $this->redirectToRoute('character_index');
        }

        return $this->render('character/edit.html.twig', [
            'character' => $character,
            'form'      => $form,
        ]);
    }

    #[Route('/{id}', name: 'character_show', methods: ['GET'])]
    public function show(Character $character): Response
    {
        // Suppression de la restriction getUser() pour permettre la vue par tous
        return $this->render('character/show.html.twig', [
            'character' => $character,
        ]);
    }

    #[Route('/{id}', name: 'character_delete', methods: ['POST'])]
    public function delete(Request $request, Character $character, EntityManagerInterface $entityManager): Response
    {
        if ($character->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Action non autorisée.");
        }

        if ($this->isCsrfTokenValid('delete'.$character->getId(), $request->request->get('_token'))) {
            $entityManager->remove($character);
            $entityManager->flush();
        }

        return $this->redirectToRoute('character_index');
    }
}
