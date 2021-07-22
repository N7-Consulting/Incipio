<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210712151351 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Processus ADD pilote_id INT DEFAULT NULL, DROP pilote');
        $this->addSql('ALTER TABLE Processus ADD CONSTRAINT FK_6C1B0EBEF510AAE9 FOREIGN KEY (pilote_id) REFERENCES Personne (id)');
        $this->addSql('CREATE INDEX IDX_6C1B0EBEF510AAE9 ON Processus (pilote_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Processus DROP FOREIGN KEY FK_6C1B0EBEF510AAE9');
        $this->addSql('DROP INDEX IDX_6C1B0EBEF510AAE9 ON Processus');
        $this->addSql('ALTER TABLE Processus ADD pilote VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP pilote_id');
    }
}
