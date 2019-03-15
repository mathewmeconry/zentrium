<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20160522204155 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE map (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, center_latitude DOUBLE PRECISION NOT NULL, center_longitude DOUBLE PRECISION NOT NULL, zoom INT NOT NULL, `default` TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE layer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, capabilities_url VARCHAR(255) DEFAULT NULL, layer_id VARCHAR(255) DEFAULT NULL, capabilities LONGBLOB DEFAULT NULL COMMENT \'(DC2Type:json_deflate)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE map_layer (id INT AUTO_INCREMENT NOT NULL, map_id INT NOT NULL, layer_id INT NOT NULL, position INT NOT NULL, opacity DOUBLE PRECISION NOT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_642D6E7653C55F64 (map_id), INDEX IDX_642D6E76EA6EFDCD (layer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE map_layer ADD CONSTRAINT FK_642D6E7653C55F64 FOREIGN KEY (map_id) REFERENCES map (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE map_layer ADD CONSTRAINT FK_642D6E76EA6EFDCD FOREIGN KEY (layer_id) REFERENCES layer (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE map_layer DROP FOREIGN KEY FK_642D6E7653C55F64');
        $this->addSql('ALTER TABLE map_layer DROP FOREIGN KEY FK_642D6E76EA6EFDCD');
        $this->addSql('DROP TABLE map');
        $this->addSql('DROP TABLE layer');
        $this->addSql('DROP TABLE map_layer');
    }
}
