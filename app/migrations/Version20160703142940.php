<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20160703142940 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE oaf_kiosk_slide (id INT AUTO_INCREMENT NOT NULL, kiosk_id INT NOT NULL, type VARCHAR(20) NOT NULL, options LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', duration INT NOT NULL, hidden TINYINT(1) NOT NULL, `order` INT NOT NULL, INDEX IDX_FE876BAC47A2102 (kiosk_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE oaf_kiosk_slide ADD CONSTRAINT FK_FE876BAC47A2102 FOREIGN KEY (kiosk_id) REFERENCES oaf_kiosk (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE oaf_kiosk_slide');
    }
}
