<?php

namespace App\Entity;

use App\Repository\PartyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PartyRepository::class)]
class Party
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['party:read', 'character:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['party:read', 'character:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['party:read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\Positive]
    #[Groups(['party:read'])]
    private ?int $maxSize = null;

    #[ORM\ManyToOne(inversedBy: 'parties')]
    #[Groups(['party:read'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, Character>
     */
    #[ORM\ManyToMany(targetEntity: Character::class, mappedBy: 'parties')]
    #[Groups(['party:read'])]
    private Collection $characters;

    public function __construct()
    {
        $this->characters = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }
    public function getMaxSize(): ?int { return $this->maxSize; }
    public function setMaxSize(int $maxSize): static { $this->maxSize = $maxSize; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    public function getCharacters(): Collection { return $this->characters; }
    public function addCharacter(Character $character): static {
        if (!$this->characters->contains($character)) {
            $this->characters->add($character);
            $character->addParty($this);
        }
        return $this;
    }
    public function removeCharacter(Character $character): static {
        if ($this->characters->removeElement($character)) {
            $character->removeParty($this);
        }
        return $this;
    }
}
