<?php
namespace App\Entity;

use App\Repository\ReclamationsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use StatutReclamation;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: ReclamationsRepository::class)]
#[ORM\HasLifecycleCallbacks] 
class Reclamations
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid")]
    private ?Uuid $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateReclamation = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The content cannot be empty.")]
    #[Assert\Regex(
        pattern: "/^\S+\s+\S+.*$/",
        message: "The content must contain at least two words."
    )]
    private ?string $description = null;

    #[ORM\Column(enumType: StatutReclamation::class)]
    private StatutReclamation $statut;

    /**
     * @var Collection<int, MessageReclamation>
     */
    #[ORM\OneToMany(targetEntity: MessageReclamation::class, mappedBy: 'reclamation')]
    private Collection $reclamations;

    public function __construct()
    {
        $this->reclamations = new ArrayCollection();
        $this->statut = StatutReclamation::EN_ATTENTE;
    }

    #[ORM\PrePersist] 
    public function generateUuid(): void
    {
        if ($this->id === null) {
            $this->id = Uuid::v4();  
        }
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getDateReclamation(): ?\DateTimeInterface
    {
        return $this->dateReclamation;
    }

    public function setDateReclamation(\DateTimeInterface $dateReclamation): static
    {
        $this->dateReclamation = $dateReclamation;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getStatut(): StatutReclamation
    {
        return $this->statut;
    }

    public function setStatut(StatutReclamation $statut): void
    {
        $this->statut = $statut;
    }

    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(MessageReclamation $reclamation): static
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->setReclamation($this);
        }
        return $this;
    }

    public function removeReclamation(MessageReclamation $reclamation): static
    {
        if ($this->reclamations->removeElement($reclamation)) {
            if ($reclamation->getReclamation() === $this) {
                $reclamation->setReclamation(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->description;
    }
}
