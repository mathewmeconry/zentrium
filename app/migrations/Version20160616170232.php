<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20160616170232 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_43DAFEBF5E237E06 ON requirement_set (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A3811FB5E237E06 ON schedule (name)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_43DAFEBF5E237E06 ON requirement_set');
        $this->addSql('DROP INDEX UNIQ_5A3811FB5E237E06 ON schedule');
    }
}
