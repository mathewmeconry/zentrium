<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20160522205254 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE position (id INT AUTO_INCREMENT NOT NULL, device VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, time DATETIME NOT NULL, server_time DATETIME NOT NULL, attributes LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE feature ADD last_position_id INT DEFAULT NULL, ADD device VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE feature ADD CONSTRAINT FK_1FD775669DB2514A FOREIGN KEY (last_position_id) REFERENCES position (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_1FD775669DB2514A ON feature (last_position_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE feature DROP FOREIGN KEY FK_1FD775669DB2514A');
        $this->addSql('DROP TABLE position');
        $this->addSql('DROP INDEX IDX_1FD775669DB2514A ON feature');
        $this->addSql('ALTER TABLE feature DROP last_position_id, DROP device');
    }
}
