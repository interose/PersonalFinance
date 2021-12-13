<?php

namespace App\Repository;

use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * @param int    $id
     * @param string $username
     * @param string $password
     * @param string $key
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function saveEncrypted(int $id, string $username, string $password, string $key)
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = 'UPDATE account SET username = AES_ENCRYPT(:username, :key), password = AES_ENCRYPT(:password, :key) WHERE id = :id';

        $stmt = $connection->prepare($sql);
        $stmt->executeStatement([
            'id' => $id,
            'username' => $username,
            'password' => $password,
            'key' => $key,
        ]);
    }

    /**
     * @param int    $id
     * @param string $key
     *
     * @return false|array
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getEncrypted(int $id, string $key): array|bool
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = 'SELECT AES_DECRYPT(username, :key) AS username, AES_DECRYPT(password, :key) AS password FROM account WHERE id = :id';

        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery([
            'id' => $id,
            'key' => $key,
        ]);

        return $result->fetchNumeric();
    }
}
