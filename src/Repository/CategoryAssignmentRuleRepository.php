<?php

namespace App\Repository;

use App\Entity\CategoryAssignmentRule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategoryAssignmentRule|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryAssignmentRule|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryAssignmentRule[]    findAll()
 * @method CategoryAssignmentRule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryAssignmentRuleRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryAssignmentRule::class);
    }
}
