<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20160613142335 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE feature DROP FOREIGN KEY FK_1FD77566EA6EFDCD');
        $this->addSql('ALTER TABLE map_layer DROP FOREIGN KEY FK_642D6E76EA6EFDCD');
        $this->addSql('ALTER TABLE log_label DROP FOREIGN KEY FK_2ED9BBB533B92F39');
        $this->addSql('ALTER TABLE log_label DROP FOREIGN KEY FK_2ED9BBB5EA675D86');
        $this->addSql('ALTER TABLE map_layer DROP FOREIGN KEY FK_642D6E7653C55F64');
        $this->addSql('ALTER TABLE feature DROP FOREIGN KEY FK_1FD775669DB2514A');
        $this->addSql('DROP TABLE feature');
        $this->addSql('DROP TABLE layer');
        $this->addSql('DROP TABLE log_label');
        $this->addSql('DROP TABLE log_labels');
        $this->addSql('DROP TABLE logs');
        $this->addSql('DROP TABLE map');
        $this->addSql('DROP TABLE map_layer');
        $this->addSql('DROP TABLE position');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE feature (id INT AUTO_INCREMENT NOT NULL, last_position_id INT DEFAULT NULL, layer_id INT NOT NULL, type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, coordinates LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\', attributes LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\', device VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_1FD77566EA6EFDCD (layer_id), INDEX IDX_1FD775669DB2514A (last_position_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE layer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, capabilities_url VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, layer_id VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, capabilities LONGBLOB DEFAULT NULL COMMENT \'(DC2Type:json_deflate)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log_label (log_id INT NOT NULL, label_id INT NOT NULL, INDEX IDX_2ED9BBB5EA675D86 (log_id), INDEX IDX_2ED9BBB533B92F39 (label_id), PRIMARY KEY(log_id, label_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log_labels (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL COLLATE utf8_unicode_ci, color VARCHAR(10) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logs (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, status VARCHAR(20) NOT NULL COLLATE utf8_unicode_ci, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE map (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, center_latitude DOUBLE PRECISION NOT NULL, center_longitude DOUBLE PRECISION NOT NULL, zoom INT NOT NULL, default_ TINYINT(1) NOT NULL, projection VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE map_layer (id INT AUTO_INCREMENT NOT NULL, layer_id INT NOT NULL, map_id INT NOT NULL, position INT NOT NULL, opacity DOUBLE PRECISION NOT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_642D6E7653C55F64 (map_id), INDEX IDX_642D6E76EA6EFDCD (layer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE position (id INT AUTO_INCREMENT NOT NULL, device VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, time DATETIME NOT NULL, server_time DATETIME NOT NULL, attributes LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE feature ADD CONSTRAINT FK_1FD775669DB2514A FOREIGN KEY (last_position_id) REFERENCES position (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE feature ADD CONSTRAINT FK_1FD77566EA6EFDCD FOREIGN KEY (layer_id) REFERENCES layer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE log_label ADD CONSTRAINT FK_2ED9BBB533B92F39 FOREIGN KEY (label_id) REFERENCES log_labels (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE log_label ADD CONSTRAINT FK_2ED9BBB5EA675D86 FOREIGN KEY (log_id) REFERENCES logs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE map_layer ADD CONSTRAINT FK_642D6E76EA6EFDCD FOREIGN KEY (layer_id) REFERENCES layer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE map_layer ADD CONSTRAINT FK_642D6E7653C55F64 FOREIGN KEY (map_id) REFERENCES map (id) ON DELETE CASCADE');
    }
}
