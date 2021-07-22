<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210719133712 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Alumnus (id INT AUTO_INCREMENT NOT NULL, personne_id INT DEFAULT NULL, commentaire VARCHAR(255) DEFAULT NULL, lienLinkedIn VARCHAR(255) DEFAULT NULL, posteActuel VARCHAR(255) DEFAULT NULL, secteurActuel_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_215565ECA21BD112 (personne_id), INDEX IDX_215565EC4102CFC8 (secteurActuel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE AlumnusContact (id INT AUTO_INCREMENT NOT NULL, alumnus_id INT NOT NULL, date DATETIME DEFAULT NULL, objet VARCHAR(255) DEFAULT NULL, contenu VARCHAR(255) DEFAULT NULL, moyenContact VARCHAR(255) DEFAULT NULL, faitPar_id INT NOT NULL, INDEX IDX_F79B05CE8A6FD59C (alumnus_id), INDEX IDX_F79B05CE5302E431 (faitPar_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE SecteurActivite (id INT AUTO_INCREMENT NOT NULL, intitule VARCHAR(127) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Alumnus ADD CONSTRAINT FK_215565ECA21BD112 FOREIGN KEY (personne_id) REFERENCES Membre (id)');
        $this->addSql('ALTER TABLE Alumnus ADD CONSTRAINT FK_215565EC4102CFC8 FOREIGN KEY (secteurActuel_id) REFERENCES SecteurActivite (id)');
        $this->addSql('ALTER TABLE AlumnusContact ADD CONSTRAINT FK_F79B05CE8A6FD59C FOREIGN KEY (alumnus_id) REFERENCES Alumnus (id)');
        $this->addSql('ALTER TABLE AlumnusContact ADD CONSTRAINT FK_F79B05CE5302E431 FOREIGN KEY (faitPar_id) REFERENCES Personne (id)');
        $this->addSql('ALTER TABLE Prospect ADD mail VARCHAR(255) DEFAULT NULL, ADD secteurActivite_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Prospect ADD CONSTRAINT FK_30B8EE2B4ED73C6D FOREIGN KEY (secteurActivite_id) REFERENCES SecteurActivite (id)');
        $this->addSql('CREATE INDEX IDX_30B8EE2B4ED73C6D ON Prospect (secteurActivite_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE AlumnusContact DROP FOREIGN KEY FK_F79B05CE8A6FD59C');
        $this->addSql('ALTER TABLE Alumnus DROP FOREIGN KEY FK_215565EC4102CFC8');
        $this->addSql('ALTER TABLE Prospect DROP FOREIGN KEY FK_30B8EE2B4ED73C6D');
        $this->addSql('DROP TABLE Alumnus');
        $this->addSql('DROP TABLE AlumnusContact');
        $this->addSql('DROP TABLE SecteurActivite');
        $this->addSql('DROP INDEX IDX_30B8EE2B4ED73C6D ON Prospect');
        $this->addSql('ALTER TABLE Prospect DROP mail, DROP secteurActivite_id');
    }
}
