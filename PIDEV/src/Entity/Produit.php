<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: "text")]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $prixUnitaire = null;

    #[ORM\Column(length: 255)]
    private ?string $urlImageProduit = null;

    #[ORM\ManyToOne(targetEntity: Categorie::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }

    public function getPrixUnitaire(): ?float { return $this->prixUnitaire; }
    public function setPrixUnitaire(float $prixUnitaire): self { $this->prixUnitaire = $prixUnitaire; return $this; }

    public function getUrlImageProduit(): ?string { return $this->urlImageProduit; }
    public function setUrlImageProduit(string $urlImageProduit): self { $this->urlImageProduit = $urlImageProduit; return $this; }

    public function getCategorie(): ?Categorie { return $this->categorie; }
    public function setCategorie(?Categorie $categorie): self { $this->categorie = $categorie; return $this; }
}
