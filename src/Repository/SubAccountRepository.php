<?php

namespace App\Repository;

use App\Entity\SubAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SubAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubAccount[]    findAll()
 * @method SubAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubAccountRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubAccount::class);
    }
}
