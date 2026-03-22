<?php

namespace App\Controller;

use App\Entity\CharacterClass;
use App\Form\CharacterClassType;
use App\Repository\CharacterClassRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/character/class')]
final class CharacterClassController extends AbstractController
{
    #[Route(name: 'app_character_class_index', methods: ['GET'])]
    public function index(CharacterClassRepository $characterClassRepository): Response
    {
        return $this->render('character_class/index.html.twig', [
            'character_classes' => $characterClassRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_character_class_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $characterClass = new CharacterClass();
        $form = $this->createForm(CharacterClassType::class, $characterClass);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($characterClass);
            $entityManager->flush();

            return $this->redirectToRoute('app_character_class_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('character_class/new.html.twig', [
            'character_class' => $characterClass,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_character_class_show', methods: ['GET'])]
    public function show(CharacterClass $characterClass): Response
    {
        return $this->render('character_class/show.html.twig', [
            'character_class' => $characterClass,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_character_class_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CharacterClass $characterClass, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CharacterClassType::class, $characterClass);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_character_class_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('character_class/edit.html.twig', [
            'character_class' => $characterClass,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_character_class_delete', methods: ['POST'])]
    public function delete(Request $request, CharacterClass $characterClass, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$characterClass->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($characterClass);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_character_class_index', [], Response::HTTP_SEE_OTHER);
    }
}
