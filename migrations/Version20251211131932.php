<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251211131932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_item DROP INDEX UNIQ_F0FE25274584665A, ADD INDEX IDX_F0FE25274584665A (product_id)');
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY `FK_F0FE25274584665A`');
        $this->addSql('ALTER TABLE cart_item CHANGE product_id product_id INT NOT NULL');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25274584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_item DROP INDEX IDX_F0FE25274584665A, ADD UNIQUE INDEX UNIQ_F0FE25274584665A (product_id)');
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE25274584665A');
        $this->addSql('ALTER TABLE cart_item CHANGE product_id product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT `FK_F0FE25274584665A` FOREIGN KEY (product_id) REFERENCES product (id)');
    }
}
