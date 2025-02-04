<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250114232049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add name column into tables Artist, Designer, Developer, Family, Graphic_designer, Honor, Mechanic, Publisher, Subdomain';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE designer ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE developer ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE family ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE graphic_designer ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE honor ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE mechanic ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE publisher ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE subdomain ADD name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subdomain DROP name');
        $this->addSql('ALTER TABLE publisher DROP name');
        $this->addSql('ALTER TABLE mechanic DROP name');
        $this->addSql('ALTER TABLE honor DROP name');
        $this->addSql('ALTER TABLE graphic_designer DROP name');
        $this->addSql('ALTER TABLE family DROP name');
        $this->addSql('ALTER TABLE developer DROP name');
        $this->addSql('ALTER TABLE designer DROP name');
        $this->addSql('ALTER TABLE artist DROP name');
    }
}
