<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250124132242 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_15996875E237E06 ON artist (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C15E237E06 ON category (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B3A0DE5B5E237E06 ON designer (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_65FB8B9A5E237E06 ON developer (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A5E6215B5E237E06 ON family (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6981C5EC5E237E06 ON graphic_designer (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1202AD965E237E06 ON honor (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7137DE795E237E06 ON mechanic (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9CE8D5465E237E06 ON publisher (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C1D5962E5E237E06 ON subdomain (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_15996875E237E06 ON artist');
        $this->addSql('DROP INDEX UNIQ_6981C5EC5E237E06 ON graphic_designer');
        $this->addSql('DROP INDEX UNIQ_1202AD965E237E06 ON honor');
        $this->addSql('DROP INDEX UNIQ_7137DE795E237E06 ON mechanic');
        $this->addSql('DROP INDEX UNIQ_A5E6215B5E237E06 ON family');
        $this->addSql('DROP INDEX UNIQ_9CE8D5465E237E06 ON publisher');
        $this->addSql('DROP INDEX UNIQ_65FB8B9A5E237E06 ON developer');
        $this->addSql('DROP INDEX UNIQ_C1D5962E5E237E06 ON subdomain');
        $this->addSql('DROP INDEX UNIQ_B3A0DE5B5E237E06 ON designer');
        $this->addSql('DROP INDEX UNIQ_64C19C15E237E06 ON category');
    }
}
