<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250215160450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE honor_game CHANGE honor_id honor_id INT DEFAULT NULL, CHANGE game_id game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mystery_game CHANGE subdomains_indices subdomains_indices LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE honor_game CHANGE honor_id honor_id INT NOT NULL, CHANGE game_id game_id INT NOT NULL');
        $this->addSql('ALTER TABLE mystery_game CHANGE subdomains_indices subdomains_indices LONGTEXT NOT NULL');
    }
}
