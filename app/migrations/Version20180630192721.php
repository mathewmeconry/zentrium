<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180630192721 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE oaf_entry_approval (id INT NOT NULL, attester_id INT DEFAULT NULL, signature LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', INDEX IDX_9B31F05074ECE899 (attester_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE oaf_entry_approval ADD CONSTRAINT FK_9B31F050BF396750 FOREIGN KEY (id) REFERENCES entry (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oaf_entry_approval ADD CONSTRAINT FK_9B31F05074ECE899 FOREIGN KEY (attester_id) REFERENCES users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE oaf_entry_approval');
    }
}
