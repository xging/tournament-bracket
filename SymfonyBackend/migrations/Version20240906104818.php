<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240906104818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE teams_match ADD place VARCHAR(255) DEFAULT NULL, CHANGE round_1_flag round_1_flag TINYINT(1) DEFAULT NULL, CHANGE round_2_flag round_2_flag TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE teams_match DROP place, CHANGE round_1_flag round_1_flag VARCHAR(255) DEFAULT NULL, CHANGE round_2_flag round_2_flag VARCHAR(255) DEFAULT NULL');
    }
}
