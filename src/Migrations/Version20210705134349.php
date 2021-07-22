<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210705134349 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Ap DROP FOREIGN KEY FK_F8BE1D87E2904019');
        $this->addSql('DROP INDEX UNIQ_F8BE1D87E2904019 ON Ap');
        $this->addSql('ALTER TABLE Ap DROP thread_id');
        $this->addSql('ALTER TABLE Av DROP FOREIGN KEY FK_11DDB8B2E2904019');
        $this->addSql('DROP INDEX UNIQ_11DDB8B2E2904019 ON Av');
        $this->addSql('ALTER TABLE Av DROP thread_id');
        $this->addSql('ALTER TABLE AvMission DROP FOREIGN KEY FK_87698F23E2904019');
        $this->addSql('DROP INDEX UNIQ_87698F23E2904019 ON AvMission');
        $this->addSql('ALTER TABLE AvMission DROP thread_id');
        $this->addSql('DROP INDEX UNIQ_59751EFDE2904019 ON Bdc');
        $this->addSql('ALTER TABLE Bdc DROP thread_id');
        $this->addSql('ALTER TABLE Cc DROP FOREIGN KEY FK_4E363EDBE2904019');
        $this->addSql('DROP INDEX UNIQ_4E363EDBE2904019 ON Cc');
        $this->addSql('ALTER TABLE Cc DROP thread_id');
        $this->addSql('DROP INDEX UNIQ_F9F88321E2904019 ON Cca');
        $this->addSql('ALTER TABLE Cca DROP thread_id');
        $this->addSql('ALTER TABLE Ce DROP FOREIGN KEY FK_A7559BEEE2904019');
        $this->addSql('DROP INDEX UNIQ_A7559BEEE2904019 ON Ce');
        $this->addSql('ALTER TABLE Ce DROP thread_id');
        $this->addSql('ALTER TABLE Mission DROP FOREIGN KEY FK_5FDACBA0E2904019');
        $this->addSql('DROP INDEX UNIQ_5FDACBA0E2904019 ON Mission');
        $this->addSql('ALTER TABLE Mission DROP thread_id');
        $this->addSql('ALTER TABLE ProcesVerbal DROP FOREIGN KEY FK_D8EBE2BFE2904019');
        $this->addSql('DROP INDEX UNIQ_D8EBE2BFE2904019 ON ProcesVerbal');
        $this->addSql('ALTER TABLE ProcesVerbal DROP thread_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Ap ADD thread_id VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE Ap ADD CONSTRAINT FK_F8BE1D87E2904019 FOREIGN KEY (thread_id) REFERENCES Thread (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F8BE1D87E2904019 ON Ap (thread_id)');
        $this->addSql('ALTER TABLE Av ADD thread_id VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE Av ADD CONSTRAINT FK_11DDB8B2E2904019 FOREIGN KEY (thread_id) REFERENCES Thread (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_11DDB8B2E2904019 ON Av (thread_id)');
        $this->addSql('ALTER TABLE AvMission ADD thread_id VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE AvMission ADD CONSTRAINT FK_87698F23E2904019 FOREIGN KEY (thread_id) REFERENCES Thread (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_87698F23E2904019 ON AvMission (thread_id)');
        $this->addSql('ALTER TABLE Bdc ADD thread_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_59751EFDE2904019 ON Bdc (thread_id)');
        $this->addSql('ALTER TABLE Cc ADD thread_id VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE Cc ADD CONSTRAINT FK_4E363EDBE2904019 FOREIGN KEY (thread_id) REFERENCES Thread (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E363EDBE2904019 ON Cc (thread_id)');
        $this->addSql('ALTER TABLE Cca ADD thread_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F9F88321E2904019 ON Cca (thread_id)');
        $this->addSql('ALTER TABLE Ce ADD thread_id VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE Ce ADD CONSTRAINT FK_A7559BEEE2904019 FOREIGN KEY (thread_id) REFERENCES Thread (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A7559BEEE2904019 ON Ce (thread_id)');
        $this->addSql('ALTER TABLE Mission ADD thread_id VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE Mission ADD CONSTRAINT FK_5FDACBA0E2904019 FOREIGN KEY (thread_id) REFERENCES Thread (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5FDACBA0E2904019 ON Mission (thread_id)');
        $this->addSql('ALTER TABLE ProcesVerbal ADD thread_id VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE ProcesVerbal ADD CONSTRAINT FK_D8EBE2BFE2904019 FOREIGN KEY (thread_id) REFERENCES Thread (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D8EBE2BFE2904019 ON ProcesVerbal (thread_id)');
    }
}
