<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20160522204933 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE feature (id INT AUTO_INCREMENT NOT NULL, layer_id INT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, coordinates LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', attributes LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', INDEX IDX_1FD77566EA6EFDCD (layer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE feature ADD CONSTRAINT FK_1FD77566EA6EFDCD FOREIGN KEY (layer_id) REFERENCES layer (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE feature');
    }
}
