<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250116131134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update HonorGame table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE honor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE honor_game (id INT AUTO_INCREMENT NOT NULL, honor_id_id INT DEFAULT NULL, game_id_id INT DEFAULT NULL, year VARCHAR(4) NOT NULL, UNIQUE INDEX UNIQ_E439F64235F6327 (honor_id_id), UNIQUE INDEX UNIQ_E439F6424D77E7D8 (game_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE honor_game ADD CONSTRAINT FK_E439F64235F6327 FOREIGN KEY (honor_id_id) REFERENCES honor (id)');
        $this->addSql('ALTER TABLE honor_game ADD CONSTRAINT FK_E439F6424D77E7D8 FOREIGN KEY (game_id_id) REFERENCES game (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE honor_game DROP FOREIGN KEY FK_E439F64235F6327');
        $this->addSql('ALTER TABLE honor_game DROP FOREIGN KEY FK_E439F6424D77E7D8');
        $this->addSql('DROP TABLE honor');
        $this->addSql('DROP TABLE honor_game');
    }
}
