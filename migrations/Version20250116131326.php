<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250116131326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE honor_game DROP FOREIGN KEY FK_E439F6424D77E7D8');
        $this->addSql('ALTER TABLE honor_game DROP FOREIGN KEY FK_E439F64235F6327');
        $this->addSql('DROP INDEX UNIQ_E439F64235F6327 ON honor_game');
        $this->addSql('DROP INDEX UNIQ_E439F6424D77E7D8 ON honor_game');
        $this->addSql('ALTER TABLE honor_game ADD honor_id INT DEFAULT NULL, ADD game_id INT DEFAULT NULL, DROP honor_id_id, DROP game_id_id');
        $this->addSql('ALTER TABLE honor_game ADD CONSTRAINT FK_E439F6426A78A2E8 FOREIGN KEY (honor_id) REFERENCES honor (id)');
        $this->addSql('ALTER TABLE honor_game ADD CONSTRAINT FK_E439F642E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E439F6426A78A2E8 ON honor_game (honor_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E439F642E48FD905 ON honor_game (game_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE honor_game DROP FOREIGN KEY FK_E439F6426A78A2E8');
        $this->addSql('ALTER TABLE honor_game DROP FOREIGN KEY FK_E439F642E48FD905');
        $this->addSql('DROP INDEX UNIQ_E439F6426A78A2E8 ON honor_game');
        $this->addSql('DROP INDEX UNIQ_E439F642E48FD905 ON honor_game');
        $this->addSql('ALTER TABLE honor_game ADD honor_id_id INT DEFAULT NULL, ADD game_id_id INT DEFAULT NULL, DROP honor_id, DROP game_id');
        $this->addSql('ALTER TABLE honor_game ADD CONSTRAINT FK_E439F6424D77E7D8 FOREIGN KEY (game_id_id) REFERENCES game (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE honor_game ADD CONSTRAINT FK_E439F64235F6327 FOREIGN KEY (honor_id_id) REFERENCES honor (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E439F64235F6327 ON honor_game (honor_id_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E439F6424D77E7D8 ON honor_game (game_id_id)');
    }
}
