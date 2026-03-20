<?php

namespace App\Controller;

use App\Entity\Party;
use App\Entity\Character;
use App\Form\PartyType;
use App\Repository\PartyRepository;
use App\Repository\CharacterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/party')]
class PartyController extends AbstractController
{
    #[Route('', name: 'party_index')]
    #[IsGranted('ROLE_USER')]
    public function index(PartyRepository $partyRepository): Response
    {
        return $this->render('party/index.html.twig', [
            'parties' => $partyRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'party_show')]
    #[IsGranted('ROLE_USER')]
    public function show(Party $party, CharacterRepository $characterRepository): Response
    {
        $userCharacters = $characterRepository->findBy(['user' => $this->getUser()]);

        return $this->render('party/GroupeCharacter.html.twig', [
            'party' => $party,
            'userCharacters' => $userCharacters,
        ]);
    }

    #[Route('/{id}/join/{characterId}', name: 'party_join')]
    #[IsGranted('ROLE_USER')]
    public function join(Party $party, Character $character, EntityManagerInterface $em): Response
    {
        if ($character->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($party->getCharacters()->count() < $party->getMaxSize()) {
            $party->addCharacter($character);
            $em->flush();
        }

        return $this->redirectToRoute('party_show', ['id' => $party->getId()]);
    }

    #[Route('/{id}/leave/{characterId}', name: 'party_leave')]
    #[IsGranted('ROLE_USER')]
    public function leave(Party $party, Character $character, EntityManagerInterface $em): Response
    {
        if ($character->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $party->removeCharacter($character);
        $em->flush();

        return $this->redirectToRoute('party_show', ['id' => $party->getId()]);
    }


    #[Route('/new', name: 'party_new')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $party = new Party();
        $party->setUser($this->getUser());
        $form = $this->createForm(PartyType::class, $party);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($party);
            $em->flush();

            return $this->redirectToRoute('party_show', ['id' => $party->getId()]);
        }

        return $this->render('party/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
