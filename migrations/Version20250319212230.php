<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250319212230 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add new table GameScore with the GameScore Entity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_score (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, game_id INT DEFAULT NULL, score INT DEFAULT NULL, progression INT DEFAULT NULL, year_published VARCHAR(255) NOT NULL, min_players VARCHAR(255) NOT NULL, playing_time VARCHAR(255) NOT NULL, age VARCHAR(255) NOT NULL, categories_indices LONGTEXT NOT NULL, subdomains_indices LONGTEXT NOT NULL, mechanics_indices LONGTEXT NOT NULL, designers_indices LONGTEXT NOT NULL, artists_indices LONGTEXT NOT NULL, graphic_designers_indices LONGTEXT NOT NULL, honors_indices LONGTEXT NOT NULL, publishers_indices LONGTEXT NOT NULL, develpers_indices LONGTEXT NOT NULL, search_history LONGTEXT NOT NULL, relation VARCHAR(255) NOT NULL, INDEX IDX_AA4EDEA76ED395 (user_id), INDEX IDX_AA4EDEE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_score ADD CONSTRAINT FK_AA4EDEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game_score ADD CONSTRAINT FK_AA4EDEE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE visits visits INT NOT NULL, CHANGE last_visit last_visit DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_score DROP FOREIGN KEY FK_AA4EDEA76ED395');
        $this->addSql('ALTER TABLE game_score DROP FOREIGN KEY FK_AA4EDEE48FD905');
        $this->addSql('DROP TABLE game_score');
        $this->addSql('ALTER TABLE game CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE visits visits INT DEFAULT 1 NOT NULL, CHANGE last_visit last_visit DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
