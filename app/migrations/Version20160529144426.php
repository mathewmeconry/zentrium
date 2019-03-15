<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20160529144426 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE requirement_set ADD slot_duration INT NOT NULL, ADD updated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE requirement CHANGE `from` from_ DATETIME NOT NULL, CHANGE `to` to_ DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE requirement CHANGE from_ `from` DATETIME NOT NULL, CHANGE to_ `to` DATETIME NOT NULL');
        $this->addSql('ALTER TABLE requirement_set DROP slot_duration, DROP updated');
    }
}
