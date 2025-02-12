<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class CommandeRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Commande::class);
        $this->entityManager = $entityManager;
    }

    public function addCommande(int $produitId, int $quantite): void
    {
        $commande = new Commande();
        $commande->setProduitId($produitId);
        $commande->setQuantite($quantite);

        $this->entityManager->persist($commande);
        $this->entityManager->flush();
    }

    public function getCommandes(): array
    {
        return $this->findAll();
    }

    public function removeCommande(int $id): void
    {
        $commande = $this->find($id);
        if ($commande) {
            $this->entityManager->remove($commande);
            $this->entityManager->flush();
        }
    }
}
