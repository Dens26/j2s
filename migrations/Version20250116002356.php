<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250116002356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE honor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE honor_game (id INT AUTO_INCREMENT NOT NULL, honor_id INT DEFAULT NULL, game_id INT DEFAULT NULL, year VARCHAR(4) NOT NULL, INDEX IDX_E439F6426A78A2E8 (honor_id), INDEX IDX_E439F642E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE honor_game ADD CONSTRAINT FK_E439F6426A78A2E8 FOREIGN KEY (honor_id) REFERENCES honor (id)');
        $this->addSql('ALTER TABLE honor_game ADD CONSTRAINT FK_E439F642E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE honor_game DROP FOREIGN KEY FK_E439F6426A78A2E8');
        $this->addSql('ALTER TABLE honor_game DROP FOREIGN KEY FK_E439F642E48FD905');
        $this->addSql('DROP TABLE honor');
        $this->addSql('DROP TABLE honor_game');
    }
}
