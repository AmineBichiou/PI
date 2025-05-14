<?php

namespace App\Entity;

use App\Repository\MessageReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: MessageReclamationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class MessageReclamation
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The content cannot be empty.")]
    #[Assert\Length(
        min: 10, 
        max: 500, 
        minMessage: "The Reply must be at least {{ limit }} characters long.",
        maxMessage: "The Reply cannot be longer than {{ limit }} characters."
    )]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateMessage = null;

    #[ORM\ManyToOne(inversedBy: 'reclamations')]
    private ?Reclamations $reclamation = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    #[ORM\PrePersist]
    public function ensureId(): void
    {
        if ($this->id === null) {
            $this->id = Uuid::v4()->toRfc4122(); // Generate UUID in string format
        }
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getDateMessage(): ?\DateTimeInterface
    {
        return $this->dateMessage;
    }

    public function setDateMessage(\DateTimeInterface $dateMessage): static
    {
        $this->dateMessage = $dateMessage;
        return $this;
    }

    public function getReclamation(): ?Reclamations
    {
        return $this->reclamation;
    }

    public function setReclamation(?Reclamations $reclamation): static
    {
        $this->reclamation = $reclamation;
        return $this;
    }
}
