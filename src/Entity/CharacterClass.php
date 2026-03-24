<?php

namespace App\Entity;

use App\Repository\CharacterClassRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CharacterClassRepository::class)]
class CharacterClass
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['class:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['class:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['class:read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\Positive]
    #[Groups(['class:read'])]
    private ?int $healthDice = null;

    /**
     * @var Collection<int, Skill>
     */
    #[ORM\ManyToMany(targetEntity: Skill::class, inversedBy: 'characterClasses')]
    #[Groups(['class:read'])]
    private Collection $skills;

    /**
     * @var Collection<int, Character>
     */
    #[ORM\OneToMany(targetEntity: Character::class, mappedBy: 'characterClass')]
    private Collection $characters;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
        $this->characters = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }
    public function getHealthDice(): ?int { return $this->healthDice; }
    public function setHealthDice(int $healthDice): static { $this->healthDice = $healthDice; return $this; }

    /**
     * @return Collection<int, Skill>
     */
    public function getSkills(): Collection { return $this->skills; }
    public function addSkill(Skill $skill): static { if (!$this->skills->contains($skill)) $this->skills->add($skill); return $this; }
    public function removeSkill(Skill $skill): static { $this->skills->removeElement($skill); return $this; }

    /**
     * @return Collection<int, Character>
     */
    public function getCharacters(): Collection { return $this->characters; }
    public function addCharacter(Character $character): static { if (!$this->characters->contains($character)) { $this->characters->add($character); $character->setCharacterClass($this); } return $this; }
    public function removeCharacter(Character $character): static { if ($this->characters->removeElement($character)) { if ($character->getCharacterClass() === $this) { $character->setCharacterClass(null); } } return $this; }
}