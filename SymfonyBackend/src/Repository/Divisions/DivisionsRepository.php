<?php

namespace App\Repository\Divisions;

use App\Entity\Divisions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
// use App\Repository\DDivisionsRepositoryInterface;

/**
 * @extends ServiceEntityRepository<Divisions>
 */
class DivisionsRepository extends ServiceEntityRepository implements DivisionsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Divisions::class);
    }

    /**
     * @return Divisions[]
     */
    public function findAll(): array
    {
        return parent::findAll();
    }
    
}