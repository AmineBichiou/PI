<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TagRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Reclamations;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private ?string $id = null;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    #[Assert\NotBlank(message: "The tag name cannot be empty.")]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: "The tag name must be at least {{ limit }} characters long.",
        maxMessage: "The tag name cannot be longer than {{ limit }} characters."
    )]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: "tag", targetEntity: Reclamations::class, orphanRemoval: true)]
    private Collection $reclamations;

    public function __construct()
    {
        $this->reclamations = new ArrayCollection();
        $this->id = Uuid::v4()->toRfc4122(); // Generate UUID on construction
    }

    #[ORM\PrePersist]
    public function ensureId(): void
    {
        if ($this->id === null) {
            $this->id = Uuid::v4()->toRfc4122();
        }
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;
        return $this;
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

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<int, Reclamations>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }
}