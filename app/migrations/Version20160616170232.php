<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160616170232 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_43DAFEBF5E237E06 ON requirement_set (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A3811FB5E237E06 ON schedule (name)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_43DAFEBF5E237E06 ON requirement_set');
        $this->addSql('DROP INDEX UNIQ_5A3811FB5E237E06 ON schedule');
    }
}
