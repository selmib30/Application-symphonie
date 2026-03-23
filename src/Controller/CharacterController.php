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
        $name    = $request->query->get('name');
        $classId = $request->query->get('class') ? (int) $request->query->get('class') : null;
        $raceId  = $request->query->get('race')  ? (int) $request->query->get('race')  : null;

        // Récupération avec filtres
        $characters = $characterRepository->findWithFilters(
            $name,
            $classId,
            $raceId
        );

        // 🔒 On garde uniquement les personnages de l'utilisateur connecté
        $characters = array_values(array_filter(
            $characters,
            fn($c) => $c->getUser() === $this->getUser()
        ));

        return $this->render('character/index.html.twig', [
            'characters' => $characters,
            'classes'    => $classRepository->findAll(),
            'races'      => $raceRepository->findAll(),
            'filters'    => [
                'name'  => $name,
                'class' => $classId,
                'race'  => $raceId,
            ],
        ]);
    }

    #[Route('/new', name: 'character_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $character = new Character();
        $character->setUser($this->getUser());

        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Calcul simple : par exemple 10 + modificateur de constitution
            // Ici on va juste mettre une valeur de base pour l'exemple
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

    #[Route('/{id}', name: 'character_show', methods: ['GET'])]
    public function show(Character $character): Response
    {
        $this->denyAccessUnlessGranted('view', $character);

        return $this->render('character/show.html.twig', [
            'character' => $character,
        ]);
    }

    #[Route('/{id}/edit', name: 'character_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Character $character,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('edit', $character);

        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('character_index');
        }

        return $this->render('character/edit.html.twig', [
            'character' => $character,
            'form'      => $form,
        ]);
    }

    #[Route('/{id}', name: 'character_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Character $character,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('delete', $character);

        if ($this->isCsrfTokenValid(
            'delete'.$character->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($character);
            $entityManager->flush();
        }

        return $this->redirectToRoute('character_index');
    }
}
