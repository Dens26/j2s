<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250203204305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stream_match CHANGE year_published year_published VARCHAR(255) NOT NULL, CHANGE min_players min_players VARCHAR(255) NOT NULL, CHANGE max_players max_players VARCHAR(255) NOT NULL, CHANGE playing_time playing_time VARCHAR(255) NOT NULL, CHANGE age age VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stream_match CHANGE year_published year_published INT NOT NULL, CHANGE min_players min_players INT NOT NULL, CHANGE max_players max_players INT NOT NULL, CHANGE playing_time playing_time INT NOT NULL, CHANGE age age INT NOT NULL');
    }
}
