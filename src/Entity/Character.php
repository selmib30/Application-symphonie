<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Character
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private string $name;

    #[ORM\Column]
    private string $classType;

    #[ORM\Column]
    private int $hitDice;

    #[ORM\Column]
    private int $strength;

    #[ORM\Column]
    private int $dexterity;

    #[ORM\Column]
    private int $constitution;

    #[ORM\Column]
    private int $intelligence;

    #[ORM\Column]
    private int $wisdom;

    #[ORM\Column]
    private int $charisma;

    #[ORM\Column(nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column]
    private int $hp;

    #[ORM\Column]
    private int $userId;

   
}