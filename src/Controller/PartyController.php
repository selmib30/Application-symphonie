<?php

namespace App\Controller;

use App\Entity\Party;
use App\Entity\Character;
use App\Form\PartyType;
use App\Repository\PartyRepository;
use App\Repository\CharacterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/party')]
class PartyController extends AbstractController
{
    #[Route('/{id}', name: 'party_show')]
    public function show(Party $party, CharacterRepository $characterRepository): Response
    {
        $userCharacters = $characterRepository->findBy(['user' => $this->getUser()]);

        return $this->render('party/GroupCharacter.html.twig', [
            'party' => $party,
            'userCharacters' => $userCharacters,
        ]);
    }

    #[Route('/{id}/join/{characterId}', name: 'party_join')]
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
    public function leave(Party $party, Character $character, EntityManagerInterface $em): Response
    {
        if ($character->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $party->removeCharacter($character);
        $em->flush();

        return $this->redirectToRoute('party_show', ['id' => $party->getId()]);
    }


    #[Route('/party/new', name: 'party_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $party = new Party();
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
