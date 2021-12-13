<?php

namespace App\Repository;

use App\Entity\SplitTransaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SplitTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method SplitTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method SplitTransaction[]    findAll()
 * @method SplitTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SplitTransactionRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SplitTransaction::class);
    }
}
