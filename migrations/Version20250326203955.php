<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250326203955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add createdAt to artist, category, designer, developer, family, graphic_designer, mechanic, publisher and subdmain table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist ADD created_at DATETIME NOT NULL DEFAULT NOW() COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE category ADD created_at DATETIME NOT NULL DEFAULT NOW() COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE designer ADD created_at DATETIME NOT NULL DEFAULT NOW() COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE developer ADD created_at DATETIME NOT NULL DEFAULT NOW() COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE family ADD created_at DATETIME NOT NULL DEFAULT NOW() COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE graphic_designer ADD created_at DATETIME NOT NULL DEFAULT NOW() COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE mechanic ADD created_at DATETIME NOT NULL DEFAULT NOW() COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE publisher ADD created_at DATETIME NOT NULL DEFAULT NOW() COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE subdomain ADD created_at DATETIME NOT NULL DEFAULT NOW() COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist DROP created_at');
        $this->addSql('ALTER TABLE category DROP created_at');
        $this->addSql('ALTER TABLE designer DROP created_at');
        $this->addSql('ALTER TABLE developer DROP created_at');
        $this->addSql('ALTER TABLE family DROP created_at');
        $this->addSql('ALTER TABLE graphic_designer DROP created_at');
        $this->addSql('ALTER TABLE mechanic DROP created_at');
        $this->addSql('ALTER TABLE publisher DROP created_at');
        $this->addSql('ALTER TABLE subdomain DROP created_at');
    }
}
