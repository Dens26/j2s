<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250203153846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stream_match (id INT AUTO_INCREMENT NOT NULL, year_published INT NOT NULL, min_players INT NOT NULL, max_players INT NOT NULL, playing_time INT NOT NULL, age INT NOT NULL, categories_indices LONGTEXT NOT NULL, subdomains_indices LONGTEXT NOT NULL, mechanics_indices LONGTEXT NOT NULL, designers_indices LONGTEXT NOT NULL, artists_indices LONGTEXT NOT NULL, graphic_designers_indices LONGTEXT NOT NULL, honors_indices LONGTEXT NOT NULL, publishers_indices LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE stream_match');
    }
}
