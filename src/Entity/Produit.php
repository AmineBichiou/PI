<?php
namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le nom du produit ne peut pas être vide.")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    private string $nom;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    #[Assert\NotBlank(message: "Le prix unitaire est obligatoire.")]
    #[Assert\Positive(message: "Le prix doit être un nombre positif.")]
    private float $prixUnitaire;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $urlImageProduit = null;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $dateCreation;

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: "produits")]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "La catégorie du produit est obligatoire.")]
    private ?Categorie $categorie = null;




    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->dateCreation = new \DateTime();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrixUnitaire(): float
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(float $prixUnitaire): self
    {
        $this->prixUnitaire = $prixUnitaire;
        return $this;
    }

    public function getUrlImageProduit(): ?string
    {
        return $this->urlImageProduit;
    }

    public function setUrlImageProduit(?string $urlImageProduit): self
    {
        $this->urlImageProduit = $urlImageProduit;
        return $this;
    }

    public function getDateCreation(): \DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function afficherDetails(): string
    {
        return "Produit: {$this->nom}, Prix: {$this->prixUnitaire} TND";
    }
}
