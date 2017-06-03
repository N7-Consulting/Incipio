<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170603140214 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $query = 'INSERT INTO `AdminParam` (`id`, `name`, `paramType`, `defaultValue`, `required`, `paramLabel`, 
`paramDescription`, `priority`) VALUES (NULL, \'namingConvention\', \'string\', \'nom\', \'1\',
 \'Convention de nommage des documents\', \'Quel champ d\une étude doit être utilisé dans les références à un document ?
Accepte les valeur numéro ou nom\', \'820\')';
        $this->addSql($query);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE from AdminParam where name = "namingConvention"');

    }
}
