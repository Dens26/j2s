<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250114152241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create graphic_designer table in database ';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE graphic_designer (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE graphic_designer_game (graphic_designer_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_8671D424EA8B7C20 (graphic_designer_id), INDEX IDX_8671D424E48FD905 (game_id), PRIMARY KEY(graphic_designer_id, game_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE graphic_designer_game ADD CONSTRAINT FK_8671D424EA8B7C20 FOREIGN KEY (graphic_designer_id) REFERENCES graphic_designer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE graphic_designer_game ADD CONSTRAINT FK_8671D424E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE graphic_designer_game DROP FOREIGN KEY FK_8671D424EA8B7C20');
        $this->addSql('ALTER TABLE graphic_designer_game DROP FOREIGN KEY FK_8671D424E48FD905');
        $this->addSql('DROP TABLE graphic_designer');
        $this->addSql('DROP TABLE graphic_designer_game');
    }
}
