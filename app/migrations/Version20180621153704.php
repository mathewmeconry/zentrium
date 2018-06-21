<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180621153704 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE oaf_message_delivery ADD sender_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE oaf_message_delivery ADD CONSTRAINT FK_571ED908F624B39D FOREIGN KEY (sender_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_571ED908F624B39D ON oaf_message_delivery (sender_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE oaf_message_delivery DROP FOREIGN KEY FK_571ED908F624B39D');
        $this->addSql('DROP INDEX IDX_571ED908F624B39D ON oaf_message_delivery');
        $this->addSql('ALTER TABLE oaf_message_delivery DROP sender_id');
    }
}
