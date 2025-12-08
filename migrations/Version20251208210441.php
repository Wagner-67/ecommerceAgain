<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251208210441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, total_price NUMERIC(10, 0) DEFAULT NULL, total_items INT DEFAULT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_BA388B7A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product CHANGE product_id product_id CHAR(36) NOT NULL, CHANGE price price NUMERIC(10, 2) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04AD4584665A ON product (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7A76ED395');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP INDEX UNIQ_D34A04AD4584665A ON product');
        $this->addSql('ALTER TABLE product CHANGE product_id product_id VARCHAR(255) NOT NULL, CHANGE price price NUMERIC(10, 0) NOT NULL');
    }
}
