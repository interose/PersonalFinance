<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220112141747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pay_pal_transaction (id INT AUTO_INCREMENT NOT NULL, booking_date DATE NOT NULL, booking_time TIME NOT NULL, name VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, amount INT NOT NULL, recipient VARCHAR(255) DEFAULT NULL, transaction_code VARCHAR(255) NOT NULL, article_description VARCHAR(255) DEFAULT NULL, article_number VARCHAR(255) DEFAULT NULL, associated_transaction_code VARCHAR(255) DEFAULT NULL, invoice_number VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE pay_pal_transaction');
    }
}
