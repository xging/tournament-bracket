<?php

namespace App\Repository\TeamsMatch;

use App\Entity\TeamsMatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<TeamsMatch>
 */
class TeamsMatchRepository extends ServiceEntityRepository implements TeamsMatchInterface
{
    private LoggerInterface $logger;
    public function __construct(ManagerRegistry $registry,  LoggerInterface $logger)
    {
        parent::__construct($registry, TeamsMatch::class);
        $this->logger = $logger;
        
    }

    /**
     * @param mixed $id
     * @param int|null $lockMode
     * @param int|null $lockVersion
     * @return TeamsMatch|null
     * @return array|null
     * @return array|null
     * @return bool|null
     */
    public function find(mixed $id, int|\Doctrine\DBAL\LockMode|null $lockMode = null, int|null $lockVersion = null): ?TeamsMatch
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    public function findBy(array $criteria, $orderBy = null, int|null $limit = null, int|null $offset = null): array
    {
        return parent::findBy($criteria);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }
    public function pickedFlagCount(bool $flag): bool
    {
        try {
            $qb = $this->createQueryBuilder('m')
                ->select('COUNT(m.id)')
                ->where('m.pickedFlag = :pickedFlag')
                ->setParameter('pickedFlag', $flag);

            $count = (int) $qb->getQuery()->getSingleScalarResult();

            return $count > 0;
        } catch (\Exception $e) {
            $this->logger->error('Error in pickedFlagCount method: ' . $e->getMessage());
            return false;
        }
    }

    // public function findByShortname(array $criteria): bool
    // {
    //     try {
    //         $qb = $this->createQueryBuilder('m')
    //             ->select('COUNT(m.id)')
    //             ->where('m.pickedFlag = :pickedFlag')
    //             ->setParameter('pickedFlag', $flag);

    //         $count = (int) $qb->getQuery()->getSingleScalarResult();

    //         return $count > 0;
    //     } catch (\Exception $e) {
    //         $this->logger->error('Error in pickedFlagCount method: ' . $e->getMessage());
    //         return false;
    //     }
    // }

}
