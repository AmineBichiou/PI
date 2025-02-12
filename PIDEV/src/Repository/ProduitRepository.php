<?php
namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function findAllProduits(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findProduitById($id): ?Produit
    {
        return $this->find($id);
    }

    public function save(Produit $produit, bool $flush = true): void
    {
        $this->_em->persist($produit);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function delete(Produit $produit, bool $flush = true): void
    {
        $this->_em->remove($produit);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
