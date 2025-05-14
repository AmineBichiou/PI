<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use App\Repository\CommandesHistoriquesRepository;

#[ORM\Entity(repositoryClass: CommandesHistoriquesRepository::class)]
class CommandesHistoriques
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column(type: "uuid")]
    private ?Uuid $utilisateur = null;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $dateAchat;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private float $prixTotal;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->dateAchat = new \DateTime();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Uuid
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(string|Uuid $utilisateur): self
    {
        $this->utilisateur = $utilisateur instanceof Uuid ? $utilisateur : Uuid::fromString($utilisateur);
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
}