<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use App\Repository\CommandesHistoriquesRepository;
use App\Entity\User;

#[ORM\Entity(repositoryClass: CommandesHistoriquesRepository::class)]
class CommandesHistoriques
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    private ?Uuid $id = null;

    // Utilisateur qui a passé la commande
    #[ORM\Column(type: "uuid")]
    private ?Uuid $utilisateur = null;

    // Date de la commande
    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $dateAchat;

    // Prix total de la commande
    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private float $prixTotal;

    // Relation ManyToOne avec l'entité User
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // Constructeur
    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->dateAchat = new \DateTime();
    }

    // Getters et setters
    public function getId(): ?Uuid 
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Uuid 
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Uuid $utilisateur): self 
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getDateAchat(): \DateTimeInterface 
    {
        return $this->dateAchat;
    }

    public function setDateAchat(\DateTimeInterface $dateAchat): self 
    {
        $this->dateAchat = $dateAchat;
        return $this;
    }

    public function getPrixTotal(): float 
    {
        return $this->prixTotal;
    }

    public function setPrixTotal(float $prixTotal): self 
    {
        $this->prixTotal = $prixTotal;
        return $this;
    }

    public function getUser(): ?User 
    {
        return $this->user;
    }

    public function setUser(?User $user): self 
    {
        $this->user = $user;
        return $this;
    }
}
