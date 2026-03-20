<?php

namespace App\Controller;

use App\Entity\Character;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CharacterController extends AbstractController
{
    private function getModifier(int $score): int
    {
        return floor(($score - 10) / 2);
    }

    private function validatePointBuy(array $stats): ?string
    {
        $total = array_sum($stats);

        if ($total !== 27) {
            return "Total must be 27";
        }

        foreach ($stats as $s) {
            if ($s < 8 || $s > 15) {
                return "Stats must be between 8 and 15";
            }
        }

        return null;
    }

    #[Route('/characters', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $data = $request->request;

        $stats = [
            (int)$data->get('strength'),
            (int)$data->get('dexterity'),
            (int)$data->get('constitution'),
            (int)$data->get('intelligence'),
            (int)$data->get('wisdom'),
            (int)$data->get('charisma'),
        ];

        
        if ($error = $this->validatePointBuy($stats)) {
            return $this->json(['error' => $error], 400);
        }

       
        $con = (int)$data->get('constitution');
        $hp = (int)$data->get('hitDice') + $this->getModifier($con);

        $character = new Character();
        $character->setName($data->get('name'));
        $character->setClassType($data->get('classType'));
        $character->setHitDice((int)$data->get('hitDice'));
        $character->setStrength($stats[0]);
        $character->setDexterity($stats[1]);
        $character->setConstitution($stats[2]);
        $character->setIntelligence($stats[3]);
        $character->setWisdom($stats[4]);
        $character->setCharisma($stats[5]);
        $character->setHp($hp);

       
        $character->setUserId(1);

        
        $file = $request->files->get('avatar');
        if ($file) {
            $filename = uniqid().'.'.$file->guessExtension();
            $file->move($this->getParameter('kernel.project_dir').'/public/uploads', $filename);
            $character->setAvatar($filename);
        }

        $em->persist($character);
        $em->flush();

        return $this->json($character);
    }

    #[Route('/characters', methods: ['GET'])]
    public function list(EntityManagerInterface $em): Response
    {
        return $this->json(
            $em->getRepository(Character::class)->findBy(['userId' => 1])
        );
    }

    #[Route('/characters/{id}', methods: ['PUT'])]
    public function update($id, Request $request, EntityManagerInterface $em): Response
    {
        $character = $em->getRepository(Character::class)->find($id);

        if (!$character) return $this->json(['error' => 'Not found'], 404);
        if ($character->getUserId() !== 1) return $this->json(['error' => 'Forbidden'], 403);

        $data = $request->request;

        $stats = [
            (int)$data->get('strength'),
            (int)$data->get('dexterity'),
            (int)$data->get('constitution'),
            (int)$data->get('intelligence'),
            (int)$data->get('wisdom'),
            (int)$data->get('charisma'),
        ];

        if ($error = $this->validatePointBuy($stats)) {
            return $this->json(['error' => $error], 400);
        }

        $hp = (int)$data->get('hitDice') + $this->getModifier($stats[2]);

        $character->setName($data->get('name'));
        $character->setClassType($data->get('classType'));
        $character->setHitDice((int)$data->get('hitDice'));
        $character->setStrength($stats[0]);
        $character->setDexterity($stats[1]);
        $character->setConstitution($stats[2]);
        $character->setIntelligence($stats[3]);
        $character->setWisdom($stats[4]);
        $character->setCharisma($stats[5]);
        $character->setHp($hp);

        $em->flush();

        return $this->json($character);
    }

    #[Route('/characters/{id}', methods: ['DELETE'])]
    public function delete($id, EntityManagerInterface $em): Response
    {
        $character = $em->getRepository(Character::class)->find($id);

        if (!$character) return $this->json(['error' => 'Not found'], 404);
        if ($character->getUserId() !== 1) return $this->json(['error' => 'Forbidden'], 403);

        $em->remove($character);
        $em->flush();

        return $this->json(['success' => true]);
    }
}