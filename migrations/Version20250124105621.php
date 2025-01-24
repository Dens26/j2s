<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250124105621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add translated_name to the category table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD translated_name VARCHAR(255) NOT NULL DEFAULT "default"');
        $this->addSql("ALTER TABLE category ALTER COLUMN translated_name DROP DEFAULT");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP translated_name');
    }
}
