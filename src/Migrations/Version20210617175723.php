<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210617175723 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE RepartitionJEH ADD phase_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE RepartitionJEH ADD CONSTRAINT FK_5E061BA899091188 FOREIGN KEY (phase_id) REFERENCES Phase (id)');
        $this->addSql('CREATE INDEX IDX_5E061BA899091188 ON RepartitionJEH (phase_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE RepartitionJEH DROP FOREIGN KEY FK_5E061BA899091188');
        $this->addSql('DROP INDEX IDX_5E061BA899091188 ON RepartitionJEH');
        $this->addSql('ALTER TABLE RepartitionJEH DROP phase_id');
    }
}
