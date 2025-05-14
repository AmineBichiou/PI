<?php

namespace App\Repository;

use App\Entity\CommandesHistoriques;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommandesHistoriques>
 */
class CommandesHistoriquesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommandesHistoriques::class);
    }

    /**
     * 🔄 Optionnel : Tu peux ajouter ici des méthodes personnalisées
     */
}