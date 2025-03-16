<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250128144437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add nullable:true';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mystery_game CHANGE categories_indices categories_indices LONGTEXT DEFAULT NULL, CHANGE mechanics_indices mechanics_indices LONGTEXT DEFAULT NULL, CHANGE designers_indices designers_indices LONGTEXT DEFAULT NULL, CHANGE artists_indices artists_indices LONGTEXT DEFAULT NULL, CHANGE graphic_designers_indices graphic_designers_indices LONGTEXT DEFAULT NULL, CHANGE honors_indices honors_indices LONGTEXT DEFAULT NULL, CHANGE publishers_indices publishers_indices LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mystery_game CHANGE categories_indices categories_indices LONGTEXT NOT NULL, CHANGE mechanics_indices mechanics_indices LONGTEXT NOT NULL, CHANGE designers_indices designers_indices LONGTEXT NOT NULL, CHANGE artists_indices artists_indices LONGTEXT NOT NULL, CHANGE graphic_designers_indices graphic_designers_indices LONGTEXT NOT NULL, CHANGE honors_indices honors_indices LONGTEXT NOT NULL, CHANGE publishers_indices publishers_indices LONGTEXT NOT NULL');
    }
}
