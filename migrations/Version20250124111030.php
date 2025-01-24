<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250124111030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE family ADD translated_name VARCHAR(255) NOT NULL DEFAULT "default"');
        $this->addSql('ALTER TABLE mechanic ADD translated_name VARCHAR(255) NOT NULL DEFAULT "default"');
        $this->addSql('ALTER TABLE subdomain ADD translated_name VARCHAR(255) NOT NULL DEFAULT "default"');

        $this->addSql("ALTER TABLE family ALTER COLUMN translated_name DROP DEFAULT");
        $this->addSql("ALTER TABLE mechanic ALTER COLUMN translated_name DROP DEFAULT");
        $this->addSql("ALTER TABLE subdomain ALTER COLUMN translated_name DROP DEFAULT");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mechanic DROP translated_name');
        $this->addSql('ALTER TABLE family DROP translated_name');
        $this->addSql('ALTER TABLE subdomain DROP translated_name');
    }
}
