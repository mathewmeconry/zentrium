<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20160704003246 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE entry ADD approved_by_id INT DEFAULT NULL, ADD approved_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D702D234F6A FOREIGN KEY (approved_by_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_2B219D702D234F6A ON entry (approved_by_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D702D234F6A');
        $this->addSql('DROP INDEX IDX_2B219D702D234F6A ON entry');
        $this->addSql('ALTER TABLE entry DROP approved_by_id, DROP approved_at');
    }
}
