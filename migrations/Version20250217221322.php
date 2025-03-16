<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217221322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
{
    // Ajouter les colonnes avec une valeur par défaut
    $this->addSql('ALTER TABLE game ADD created_at DATETIME NOT NULL DEFAULT NOW() COMMENT \'(DC2Type:datetime_immutable)\'');
    $this->addSql('ALTER TABLE game ADD updated_at DATETIME NOT NULL DEFAULT NOW() COMMENT \'(DC2Type:datetime_immutable)\'');
    $this->addSql('ALTER TABLE game ADD visits INT NOT NULL DEFAULT 1');
    $this->addSql('ALTER TABLE game ADD last_visit DATETIME NOT NULL DEFAULT NOW() COMMENT \'(DC2Type:datetime_immutable)\'');
}

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP created_at, DROP updated_at, DROP visits, DROP last_visit');
    }
}
