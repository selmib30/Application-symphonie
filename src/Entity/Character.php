<?php

namespace App\Entity;

use App\Repository\CharacterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CharacterRepository::class)]
class Character
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['character:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['character:read'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\Range(min: 1, max: 20)]
    #[Groups(['character:read'])]
    private ?int $level = 1;

    #[ORM\Column]
    #[Groups(['character:read'])]
    private ?int $strength = null;

    #[ORM\Column]
    #[Groups(['character:read'])]
    private ?int $dexterity = null;

    #[ORM\Column]
    #[Groups(['character:read'])]
    private ?int $constitution = null;

    #[ORM\Column]
    #[Groups(['character:read'])]
    private ?int $intelligence = null;

    #[ORM\Column]
    #[Groups(['character:read'])]
    private ?int $wisdom = null;

    #[ORM\Column]
    #[Groups(['character:read'])]
    private ?int $charisma = null;

    #[ORM\Column]
    #[Groups(['character:read'])]
    private ?int $healthPoints = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['character:read'])]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'characters')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['character:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'characters')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['character:read'])]
    private ?Race $race = null;

    #[ORM\ManyToOne(inversedBy: 'characters')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['character:read'])]
    private ?CharacterClass $characterClass = null;

    /**
     * @var Collection<int, Party>
     */
    #[ORM\ManyToMany(targetEntity: Party::class, inversedBy: 'characters')]
    #[Groups(['character:read'])]
    private Collection $parties;

    public function __construct()
    {
        $this->parties = new ArrayCollection();
    }

}