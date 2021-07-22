<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210616145406 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Passation (id INT AUTO_INCREMENT NOT NULL, categorie INT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE RelatedDocument ADD passation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE RelatedDocument ADD CONSTRAINT FK_E28BFD666893B9E9 FOREIGN KEY (passation_id) REFERENCES Passation (id)');
        $this->addSql('CREATE INDEX IDX_E28BFD666893B9E9 ON RelatedDocument (passation_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE RelatedDocument DROP FOREIGN KEY FK_E28BFD666893B9E9');
        $this->addSql('DROP TABLE Passation');
        $this->addSql('DROP INDEX IDX_E28BFD666893B9E9 ON RelatedDocument');
        $this->addSql('ALTER TABLE RelatedDocument DROP passation_id');
    }
}
