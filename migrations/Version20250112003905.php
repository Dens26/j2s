<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250112003905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new column passwordResetToken and passwordResetTokenExpiration from user entity to user table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD password_reset_token VARCHAR(255) DEFAULT NULL, ADD password_reset_token_expiration DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP password_reset_token, DROP password_reset_token_expiration');
    }
}
