<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250319220430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_score DROP FOREIGN KEY FK_AA4EDEE48FD905');
        $this->addSql('DROP INDEX IDX_AA4EDEE48FD905 ON game_score');
        $this->addSql('ALTER TABLE game_score CHANGE game_id mystery_game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game_score ADD CONSTRAINT FK_AA4EDE77722F8C FOREIGN KEY (mystery_game_id) REFERENCES mystery_game (id)');
        $this->addSql('CREATE INDEX IDX_AA4EDE77722F8C ON game_score (mystery_game_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_score DROP FOREIGN KEY FK_AA4EDE77722F8C');
        $this->addSql('DROP INDEX IDX_AA4EDE77722F8C ON game_score');
        $this->addSql('ALTER TABLE game_score CHANGE mystery_game_id game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game_score ADD CONSTRAINT FK_AA4EDEE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_AA4EDEE48FD905 ON game_score (game_id)');
    }
}
