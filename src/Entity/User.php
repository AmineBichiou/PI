<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid")]
    private ?Uuid $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $travail = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateIscri = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photoUrl = '';

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $numTel = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reclamations::class, cascade: ['persist', 'remove'])]
    private Collection $reclamations;

    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'user')]
    private Collection $produits;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Inscription::class, cascade: ['persist', 'remove'])]
    private Collection $inscriptions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CommentaireEvent::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $commentaireEvents;

    public function __construct()
    {
        $this->reclamations = new ArrayCollection();
        $this->produits = new ArrayCollection();
        $this->inscriptions = new ArrayCollection();
        $this->commentaireEvents = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getTravail(): ?string
    {
        return $this->travail;
    }

    public function setTravail(string $travail): static
    {
        $this->travail = $travail;
        return $this;
    }

    public function getDateIscri(): ?\DateTimeInterface
    {
        return $this->dateIscri;
    }

    public function setDateIscri(?\DateTimeInterface $dateIscri): static
    {
        $this->dateIscri = $dateIscri;
        return $this;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl(string $photoUrl): static
    {
        $this->photoUrl = $photoUrl;
        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getNumTel(): ?string
    {
        return $this->numTel;
    }

    public function setNumTel(?string $numTel): static
    {
        $this->numTel = $numTel;
        return $this;
    }

    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscription $inscription): self
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions->add($inscription);
            $inscription->setUser($this);
        }
        return $this;
    }

    public function removeInscription(Inscription $inscription): self
    {
        if ($this->inscriptions->removeElement($inscription)) {
            if ($inscription->getUser() === $this) {
                $inscription->setUser(null);
            }
        }
        return $this;
    }

    public function getCommentaireEvents(): Collection
    {
        return $this->commentaireEvents;
    }

    public function addCommentaireEvent(CommentaireEvent $commentaireEvent): self
    {
        if (!$this->commentaireEvents->contains($commentaireEvent)) {
            $this->commentaireEvents->add($commentaireEvent);
            $commentaireEvent->setUser($this);
        }

        return $this;
    }

    public function removeCommentaireEvent(CommentaireEvent $commentaireEvent): self
    {
        if ($this->commentaireEvents->removeElement($commentaireEvent)) {
            if ($commentaireEvent->getUser() === $this) {
                $commentaireEvent->setUser(null);
            }
        }

        return $this;
    }
}
