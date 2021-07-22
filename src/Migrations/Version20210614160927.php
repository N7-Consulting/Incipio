<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210614160927 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Processus ADD fiche_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Processus ADD CONSTRAINT FK_6C1B0EBEDF522508 FOREIGN KEY (fiche_id) REFERENCES Document (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6C1B0EBEDF522508 ON Processus (fiche_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Processus DROP FOREIGN KEY FK_6C1B0EBEDF522508');
        $this->addSql('DROP INDEX UNIQ_6C1B0EBEDF522508 ON Processus');
        $this->addSql('ALTER TABLE Processus DROP fiche_id');
    }
}
