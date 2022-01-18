<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220117143240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction ADD pay_pal_transaction_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D11275951F FOREIGN KEY (pay_pal_transaction_id) REFERENCES pay_pal_transaction (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_723705D11275951F ON transaction (pay_pal_transaction_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D11275951F');
        $this->addSql('DROP INDEX UNIQ_723705D11275951F ON transaction');
        $this->addSql('ALTER TABLE transaction DROP pay_pal_transaction_id');
    }
}
