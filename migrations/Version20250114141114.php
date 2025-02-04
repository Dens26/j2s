<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250114141114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Tables Game, Category, Developer, Designer, Family, Mechanic, Subdomain, Artist, Honor and Publisher';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artist (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE artist_game (artist_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_9F95B713B7970CF8 (artist_id), INDEX IDX_9F95B713E48FD905 (game_id), PRIMARY KEY(artist_id, game_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_game (category_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_A8B04BCB12469DE2 (category_id), INDEX IDX_A8B04BCBE48FD905 (game_id), PRIMARY KEY(category_id, game_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE designer (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE designer_game (designer_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_E1D6D1FCCFC54FAB (designer_id), INDEX IDX_E1D6D1FCE48FD905 (game_id), PRIMARY KEY(designer_id, game_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE developer (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE developer_game (developer_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_7EFB4CCF64DD9267 (developer_id), INDEX IDX_7EFB4CCFE48FD905 (game_id), PRIMARY KEY(developer_id, game_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE family (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE family_game (family_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_575C93A8C35E566A (family_id), INDEX IDX_575C93A8E48FD905 (game_id), PRIMARY KEY(family_id, game_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, name VARCHAR(255) NOT NULL, all_names VARCHAR(255) DEFAULT NULL, year_published INT DEFAULT NULL, min_players INT DEFAULT NULL, max_players INT DEFAULT NULL, playing_time INT DEFAULT NULL, age INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, thumbnail VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE honor (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE honor_game (honor_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_E439F6426A78A2E8 (honor_id), INDEX IDX_E439F642E48FD905 (game_id), PRIMARY KEY(honor_id, game_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mechanic (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mechanic_game (mechanic_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_5B47B6219A67DB00 (mechanic_id), INDEX IDX_5B47B621E48FD905 (game_id), PRIMARY KEY(mechanic_id, game_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE publisher (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE publisher_game (publisher_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_C232E1DB40C86FCE (publisher_id), INDEX IDX_C232E1DBE48FD905 (game_id), PRIMARY KEY(publisher_id, game_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subdomain (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subdomain_game (subdomain_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_C1777F5E8530A5DC (subdomain_id), INDEX IDX_C1777F5EE48FD905 (game_id), PRIMARY KEY(subdomain_id, game_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE artist_game ADD CONSTRAINT FK_9F95B713B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE artist_game ADD CONSTRAINT FK_9F95B713E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_game ADD CONSTRAINT FK_A8B04BCB12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_game ADD CONSTRAINT FK_A8B04BCBE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE designer_game ADD CONSTRAINT FK_E1D6D1FCCFC54FAB FOREIGN KEY (designer_id) REFERENCES designer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE designer_game ADD CONSTRAINT FK_E1D6D1FCE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE developer_game ADD CONSTRAINT FK_7EFB4CCF64DD9267 FOREIGN KEY (developer_id) REFERENCES developer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE developer_game ADD CONSTRAINT FK_7EFB4CCFE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE family_game ADD CONSTRAINT FK_575C93A8C35E566A FOREIGN KEY (family_id) REFERENCES family (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE family_game ADD CONSTRAINT FK_575C93A8E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE honor_game ADD CONSTRAINT FK_E439F6426A78A2E8 FOREIGN KEY (honor_id) REFERENCES honor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE honor_game ADD CONSTRAINT FK_E439F642E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mechanic_game ADD CONSTRAINT FK_5B47B6219A67DB00 FOREIGN KEY (mechanic_id) REFERENCES mechanic (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mechanic_game ADD CONSTRAINT FK_5B47B621E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE publisher_game ADD CONSTRAINT FK_C232E1DB40C86FCE FOREIGN KEY (publisher_id) REFERENCES publisher (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE publisher_game ADD CONSTRAINT FK_C232E1DBE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subdomain_game ADD CONSTRAINT FK_C1777F5E8530A5DC FOREIGN KEY (subdomain_id) REFERENCES subdomain (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subdomain_game ADD CONSTRAINT FK_C1777F5EE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist_game DROP FOREIGN KEY FK_9F95B713B7970CF8');
        $this->addSql('ALTER TABLE artist_game DROP FOREIGN KEY FK_9F95B713E48FD905');
        $this->addSql('ALTER TABLE category_game DROP FOREIGN KEY FK_A8B04BCB12469DE2');
        $this->addSql('ALTER TABLE category_game DROP FOREIGN KEY FK_A8B04BCBE48FD905');
        $this->addSql('ALTER TABLE designer_game DROP FOREIGN KEY FK_E1D6D1FCCFC54FAB');
        $this->addSql('ALTER TABLE designer_game DROP FOREIGN KEY FK_E1D6D1FCE48FD905');
        $this->addSql('ALTER TABLE developer_game DROP FOREIGN KEY FK_7EFB4CCF64DD9267');
        $this->addSql('ALTER TABLE developer_game DROP FOREIGN KEY FK_7EFB4CCFE48FD905');
        $this->addSql('ALTER TABLE family_game DROP FOREIGN KEY FK_575C93A8C35E566A');
        $this->addSql('ALTER TABLE family_game DROP FOREIGN KEY FK_575C93A8E48FD905');
        $this->addSql('ALTER TABLE honor_game DROP FOREIGN KEY FK_E439F6426A78A2E8');
        $this->addSql('ALTER TABLE honor_game DROP FOREIGN KEY FK_E439F642E48FD905');
        $this->addSql('ALTER TABLE mechanic_game DROP FOREIGN KEY FK_5B47B6219A67DB00');
        $this->addSql('ALTER TABLE mechanic_game DROP FOREIGN KEY FK_5B47B621E48FD905');
        $this->addSql('ALTER TABLE publisher_game DROP FOREIGN KEY FK_C232E1DB40C86FCE');
        $this->addSql('ALTER TABLE publisher_game DROP FOREIGN KEY FK_C232E1DBE48FD905');
        $this->addSql('ALTER TABLE subdomain_game DROP FOREIGN KEY FK_C1777F5E8530A5DC');
        $this->addSql('ALTER TABLE subdomain_game DROP FOREIGN KEY FK_C1777F5EE48FD905');
        $this->addSql('DROP TABLE artist');
        $this->addSql('DROP TABLE artist_game');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_game');
        $this->addSql('DROP TABLE designer');
        $this->addSql('DROP TABLE designer_game');
        $this->addSql('DROP TABLE developer');
        $this->addSql('DROP TABLE developer_game');
        $this->addSql('DROP TABLE family');
        $this->addSql('DROP TABLE family_game');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE honor');
        $this->addSql('DROP TABLE honor_game');
        $this->addSql('DROP TABLE mechanic');
        $this->addSql('DROP TABLE mechanic_game');
        $this->addSql('DROP TABLE publisher');
        $this->addSql('DROP TABLE publisher_game');
        $this->addSql('DROP TABLE subdomain');
        $this->addSql('DROP TABLE subdomain_game');
    }
}
