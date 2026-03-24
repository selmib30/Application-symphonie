<?php

namespace App\Entity;

use App\Repository\SkillRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SkillRepository::class)]
class Skill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 3)]
    #[Assert\Choice(choices: ['STR', 'DEX', 'CON', 'INT', 'WIS', 'CHA'])]
    private ?string $ability = null;

    /**
     * @var Collection<int, CharacterClass>
     */
    #[ORM\ManyToMany(targetEntity: CharacterClass::class, mappedBy: 'skills')]
    private Collection $characterClasses;

    public function __toString(): string
    {
        return $this->name;
    }
    public function __construct()
    {
        $this->characterClasses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAbility(): ?string
    {
        return $this->ability;
    }

    public function setAbility(string $ability): static
    {
        $this->ability = $ability;

        return $this;
    }

    /**
     * @return Collection<int, CharacterClass>
     */
    public function getCharacterClasses(): Collection
    {
        return $this->characterClasses;
    }

    public function addCharacterClass(CharacterClass $characterClass): static
    {
        if (!$this->characterClasses->contains($characterClass)) {
            $this->characterClasses->add($characterClass);
            $characterClass->addSkill($this);
        }

        return $this;
    }

    public function removeCharacterClass(CharacterClass $characterClass): static
    {
        if ($this->characterClasses->removeElement($characterClass)) {
            $characterClass->removeSkill($this);
        }

        return $this;
    }
}
