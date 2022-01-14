<?php

namespace App\Repository;

use App\Entity\PayPalTransaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PayPalTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method PayPalTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method PayPalTransaction[]    findAll()
 * @method PayPalTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PayPalTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PayPalTransaction::class);
    }

    /**
     * @param int $year
     * @return \mixed[][]
     * @throws \Doctrine\DBAL\Exception
     */
    public function getByYear(int $year)
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
SELECT id, CONCAT(booking_date,' ',booking_time) AS booking_date, name, type, ROUND(amount / 100, 2) AS amount, recipient, transaction_code, article_description, article_number, associated_transaction_code, invoice_number
FROM pay_pal_transaction
WHERE DATE_FORMAT(booking_date, "%Y") = :year
ORDER BY booking_date DESC
SQL;

        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery([
            'year' => $year
        ]);

        return $result->fetchAllAssociative();
    }

    // /**
    //  * @return PayPalTransaction[] Returns an array of PayPalTransaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PayPalTransaction
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
