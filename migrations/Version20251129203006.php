<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251129203006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE verified_token verified_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6499C24364A ON user (verified_token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64939749B5B ON user (delete_token)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D6499C24364A ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D64939749B5B ON user');
        $this->addSql('ALTER TABLE user CHANGE verified_token verified_token VARCHAR(255) NOT NULL');
    }
}
