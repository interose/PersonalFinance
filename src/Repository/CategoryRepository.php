<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return mixed
     */
    public function getAsArray(): mixed
    {
        $query = $this->createQueryBuilder('c')
            ->select('c.id AS id, c.treeIgnore AS tree_ignore, CASE WHEN cg.name IS NULL THEN c.name ELSE CONCAT(cg.name, \':\', c.name) END AS name')
            ->leftJoin('c.categoryGroup', 'cg')
            ->getQuery();

        return $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * @return mixed
     */
    public function getAllWithGroup(): mixed
    {
        return $this->createQueryBuilder('c')
            ->select('c.name, c.id, cg.name as groupName, cg.id as groupId, c.treeIgnore, c.dashboardIgnore, (CASE WHEN  cg.name = \'\' THEN 2 WHEN cg.name is null THEN 2 ELSE 1 END) AS HIDDEN ord ')
            ->leftJoin('c.categoryGroup', 'cg')
            ->addOrderBy('ord')
            ->addOrderBy('cg.name', 'ASC')
            ->addOrderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getCategoriesByGroupId(int $categoryGroupId)
    {
        $connection = $this->getEntityManager()->getConnection();
        $sql = 'SELECT id, name FROM category WHERE category_group_id = :category_group_id';

        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery([
            'category_group_id' => $categoryGroupId,
        ]);

        return $result->fetchAllKeyValue();
    }

    /**
     * @param array $ids
     *
     * @return mixed
     */
    public function getWhereIn(array $ids): mixed
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }
}
