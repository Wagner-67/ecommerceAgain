<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251220160423 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_items ADD product_name VARCHAR(255) NOT NULL, ADD product_id INT NOT NULL, ADD unit_price DOUBLE PRECISION NOT NULL, ADD quantity DOUBLE PRECISION NOT NULL, ADD total_price DOUBLE PRECISION NOT NULL, ADD orders_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB0CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id)');
        $this->addSql('CREATE INDEX IDX_62809DB0CFFE9AD6 ON order_items (orders_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB0CFFE9AD6');
        $this->addSql('DROP INDEX IDX_62809DB0CFFE9AD6 ON order_items');
        $this->addSql('ALTER TABLE order_items DROP product_name, DROP product_id, DROP unit_price, DROP quantity, DROP total_price, DROP orders_id');
    }
}
