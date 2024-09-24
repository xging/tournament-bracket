<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240902093238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE teams_match ADD quarterfinal_flag TINYINT(1) DEFAULT NULL, ADD semifinal_flag TINYINT(1) DEFAULT NULL, ADD bronzemedal_flag TINYINT(1) DEFAULT NULL, ADD grandfinal_flag TINYINT(1) DEFAULT NULL, ADD no VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE teams_match DROP quarterfinal_flag, DROP semifinal_flag, DROP bronzemedal_flag, DROP grandfinal_flag, DROP no');
    }
}
