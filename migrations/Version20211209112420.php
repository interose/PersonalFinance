<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211209112420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, account_holder VARCHAR(255) NOT NULL, iban VARCHAR(255) NOT NULL, bic VARCHAR(255) NOT NULL, bank_code VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, tan_media_name VARCHAR(255) DEFAULT NULL, tan_mechanism INT DEFAULT NULL, username VARBINARY(255) DEFAULT NULL, password VARBINARY(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, background_color VARCHAR(255) DEFAULT NULL, foreground_color VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, category_group_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, tree_ignore TINYINT(1) DEFAULT NULL, dashboard_ignore TINYINT(1) DEFAULT NULL, INDEX IDX_64C19C1492E5D3C (category_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_assignment_rule (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, rule VARCHAR(255) NOT NULL, type INT NOT NULL, transaction_field INT NOT NULL, INDEX IDX_45573D0212469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE current_balance (id INT AUTO_INCREMENT NOT NULL, sub_account_id INT NOT NULL, balance INT NOT NULL, UNIQUE INDEX UNIQ_6F99A7BB7A0CC77 (sub_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE settings (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_E545A0C55E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE split_transaction (id INT AUTO_INCREMENT NOT NULL, transaction_id INT NOT NULL, category_id INT DEFAULT NULL, amount INT NOT NULL, description VARCHAR(255) NOT NULL, valuta_date DATE NOT NULL, INDEX IDX_29F52C6F2FC0CB0F (transaction_id), INDEX IDX_29F52C6F12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sub_account (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, iban VARCHAR(255) NOT NULL, bic VARCHAR(255) NOT NULL, account_number VARCHAR(255) NOT NULL, blz VARCHAR(255) NOT NULL, is_enabled TINYINT(1) NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_2EE0A509B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, sub_account_id INT DEFAULT NULL, booking_date DATETIME NOT NULL, valuta_date DATETIME NOT NULL, amount INT NOT NULL, credit_debit VARCHAR(255) DEFAULT NULL, booking_text VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, description_raw VARCHAR(255) DEFAULT NULL, bank_code VARCHAR(255) DEFAULT NULL, account_number VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, checksum VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_723705D1DE6FDF9A (checksum), INDEX IDX_723705D112469DE2 (category_id), INDEX IDX_723705D1B7A0CC77 (sub_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transfer (id INT AUTO_INCREMENT NOT NULL, info VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, iban VARCHAR(255) NOT NULL, bic VARCHAR(255) NOT NULL, bank_name VARCHAR(255) NOT NULL, amount INT NOT NULL, execution_date DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1492E5D3C FOREIGN KEY (category_group_id) REFERENCES category_group (id)');
        $this->addSql('ALTER TABLE category_assignment_rule ADD CONSTRAINT FK_45573D0212469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE current_balance ADD CONSTRAINT FK_6F99A7BB7A0CC77 FOREIGN KEY (sub_account_id) REFERENCES sub_account (id)');
        $this->addSql('ALTER TABLE split_transaction ADD CONSTRAINT FK_29F52C6F2FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id)');
        $this->addSql('ALTER TABLE split_transaction ADD CONSTRAINT FK_29F52C6F12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE sub_account ADD CONSTRAINT FK_2EE0A509B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1B7A0CC77 FOREIGN KEY (sub_account_id) REFERENCES sub_account (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sub_account DROP FOREIGN KEY FK_2EE0A509B6B5FBA');
        $this->addSql('ALTER TABLE category_assignment_rule DROP FOREIGN KEY FK_45573D0212469DE2');
        $this->addSql('ALTER TABLE split_transaction DROP FOREIGN KEY FK_29F52C6F12469DE2');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D112469DE2');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1492E5D3C');
        $this->addSql('ALTER TABLE current_balance DROP FOREIGN KEY FK_6F99A7BB7A0CC77');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1B7A0CC77');
        $this->addSql('ALTER TABLE split_transaction DROP FOREIGN KEY FK_29F52C6F2FC0CB0F');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_assignment_rule');
        $this->addSql('DROP TABLE category_group');
        $this->addSql('DROP TABLE current_balance');
        $this->addSql('DROP TABLE settings');
        $this->addSql('DROP TABLE split_transaction');
        $this->addSql('DROP TABLE sub_account');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE transfer');
    }
}
