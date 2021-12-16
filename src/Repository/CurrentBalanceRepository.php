<?php

namespace App\Repository;

use App\Entity\CurrentBalance;
use App\Entity\SubAccount;
use App\Lib\SettingsHandler;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CurrentBalance|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurrentBalance|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurrentBalance[]    findAll()
 * @method CurrentBalance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrentBalanceRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrentBalance::class);
    }

    /**
     * @param SubAccount $subAccount
     * @param int        $balance
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateBalance(SubAccount $subAccount, int $balance)
    {
        $balanceObj = $this->createQueryBuilder('c')
            ->andWhere('c.subAccount = :val')
            ->setParameter('val', $subAccount)
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $balanceObj) {
            $balanceObj = new CurrentBalance();
            $balanceObj->setSubAccount($subAccount);
        }

        $balanceObj->setBalance($balance);

        $em = $this->getEntityManager();
        $em->persist($balanceObj);
        $em->flush();
    }

    /**
     * @return float
     */
    public function getMainAccountBalance(): float
    {
        try {
            $connection = $this->getEntityManager()->getConnection();

            $sql = 'SELECT ROUND(SUM(cb.balance) / 100, 2) FROM current_balance cb INNER JOIN (SELECT value FROM settings WHERE name = :setting_name) AS s ON cb.sub_account_id = s.value' ;
            $stmt = $connection->prepare($sql);
            $result = $stmt->executeQuery([
                'setting_name' => SettingsHandler::SETTING_MAIN_ACCOUNT,
            ]);

            $balance = $result->fetchOne();

            if (false !== $balance && !is_null($balance)) {
                return $balance;
            }
        } catch (Exception $e) {
        }

        return 0;
    }
}
