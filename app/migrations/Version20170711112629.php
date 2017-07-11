<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170711112629 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE task ADD timesheet_activity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2529409AAE FOREIGN KEY (timesheet_activity_id) REFERENCES activity (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_527EDB2529409AAE ON task (timesheet_activity_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB2529409AAE');
        $this->addSql('DROP INDEX IDX_527EDB2529409AAE ON task');
        $this->addSql('ALTER TABLE task DROP timesheet_activity_id');
    }
}
