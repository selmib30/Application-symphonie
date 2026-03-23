<?php

namespace App\Controller;

use App\Entity\Character;
use App\Form\CharacterType;
use App\Repository\CharacterClassRepository;
use App\Repository\CharacterRepository;
use App\Repository\RaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/character')]
#[IsGranted('ROLE_USER')]
class CharacterController extends AbstractController
{
    private const POINT_COST = [
        8 => 0, 9 => 1, 10 => 2, 11 => 3,
        12 => 4, 13 => 5, 14 => 7, 15 => 9,
    ];

    private const TOTAL_POINTS = 27;

    private function modifier(int $score): int
    {
        return (int) floor(($score - 10) / 2);
    }

    private function totalPointCost(Character $character): int
    {
        $stats = [
            $character->getStrength(),
            $character->getDexterity(),
            $character->getConstitution(),
            $character->getIntelligence(),
            $character->getWisdom(),
            $character->getCharisma(),
        ];

        $total = 0;
        foreach ($stats as $stat) {
            if ($stat === null || !isset(self::POINT_COST[$stat])) {
                return -1;
            }
            $total += self::POINT_COST[$stat];
        }

        return $total;
    }

    // =====================================================================
    // TACHE 1 : Recherche par nom + filtre par classe et/ou race (GET)
    // =====================================================================
    #[Route('', name: 'character_index')]
    public function index(
        Request $request,
        CharacterRepository $characterRepository,
        CharacterClassRepository $classRepository,
        RaceRepository $raceRepository
    ): Response {
        $name    = $request->query->get('name');
        $classId = $request->query->get('class') ? (int) $request->query->get('class') : null;
        $raceId  = $request->query->get('race')  ? (int) $request->query->get('race')  : null;

        $characters = $characterRepository->findWithFilters($name, $classId, $raceId);

        // On ne garde que les personnages de l'utilisateur connecté
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

    #[Route('/new', name: 'character_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $character = new Character();
        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cost = $this->totalPointCost($character);
            if ($cost !== self::TOTAL_POINTS) {
                $this->addFlash('error', sprintf(
                    'Le point buy est invalide : vous avez utilisé %d points sur %d.',
                    $cost, self::TOTAL_POINTS
                ));
                return $this->render('character/form.html.twig', [
                    'form' => $form->createView(),
                    'character' => $character,
                    'title' => 'Créer un personnage',
                ]);
            }

            $class = $character->getCharacterClass();
            $conMod = $this->modifier($character->getConstitution());
            $hp = max(1, $class->getHealthDice() + $conMod);
            $character->setHealthPoints($hp);

            $avatarFile = $form->get('avatarFile')->getData();
            if ($avatarFile) {
                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $avatarFile->guessExtension();

                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/avatars';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $avatarFile->move($uploadDir, $newFilename);
                $character->setImage('uploads/avatars/' . $newFilename);
            }

            $character->setUser($this->getUser());
            $character->setLevel(1);

            $em->persist($character);
            $em->flush();

            $this->addFlash('success', 'Personnage créé avec succès !');
            return $this->redirectToRoute('character_index');
        }

        return $this->render('character/form.html.twig', [
            'form' => $form->createView(),
            'character' => $character,
            'title' => 'Créer un personnage',
        ]);
    }

    #[Route('/api/class/{id}', name: 'character_api_class')]
    public function apiClass(int $id, CharacterClassRepository $repo): JsonResponse
    {
        $class = $repo->find($id);
        if (!$class) {
            return new JsonResponse(['error' => 'Not found'], 404);
        }
        return new JsonResponse(['healthDice' => $class->getHealthDice()]);
    }
    #[Route('/{id}/edit', name: 'character_edit')]
    public function edit(
        Character $character,
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        if ($character->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que vos propres personnages.');
        }

        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cost = $this->totalPointCost($character);
            if ($cost !== self::TOTAL_POINTS) {
                $this->addFlash('error', sprintf(
                    'Le point buy est invalide : vous avez utilisé %d points sur %d.',
                    $cost, self::TOTAL_POINTS
                ));
                return $this->render('character/form.html.twig', [
                    'form' => $form->createView(),
                    'character' => $character,
                    'title' => 'Modifier ' . $character->getName(),
                ]);
            }

            $class = $character->getCharacterClass();
            $conMod = $this->modifier($character->getConstitution());
            $hp = max(1, $class->getHealthDice() + $conMod);
            $character->setHealthPoints($hp);

            $avatarFile = $form->get('avatarFile')->getData();
            if ($avatarFile) {
                $oldImage = $character->getImage();
                if ($oldImage) {
                    $oldPath = $this->getParameter('kernel.project_dir') . '/public/' . $oldImage;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $avatarFile->guessExtension();

                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/avatars';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $avatarFile->move($uploadDir, $newFilename);
                $character->setImage('uploads/avatars/' . $newFilename);
            }

            $em->flush();

            $this->addFlash('success', 'Personnage modifié avec succès !');
            return $this->redirectToRoute('character_index');
        }

        return $this->render('character/form.html.twig', [
            'form' => $form->createView(),
            'character' => $character,
            'title' => 'Modifier ' . $character->getName(),
        ]);
    }

    #[Route('/{id}/delete', name: 'character_delete', methods: ['POST'])]
    public function delete(
        Character $character,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if ($character->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez supprimer que vos propres personnages.');
        }

        if ($this->isCsrfTokenValid('delete' . $character->getId(), $request->request->get('_token'))) {
            $image = $character->getImage();
            if ($image) {
                $imagePath = $this->getParameter('kernel.project_dir') . '/public/' . $image;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $em->remove($character);
            $em->flush();
            $this->addFlash('success', 'Personnage supprimé.');
        }

        return $this->redirectToRoute('character_index');
    }


}
