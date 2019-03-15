<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20180512082643 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE oaf_message_delivery (id INT AUTO_INCREMENT NOT NULL, message_id INT NOT NULL, user_id INT NOT NULL, number VARCHAR(35) NOT NULL COMMENT \'(DC2Type:phone_number)\', status TINYINT(1) DEFAULT NULL, extra VARCHAR(255) DEFAULT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_571ED908537A1329 (message_id), INDEX IDX_571ED908A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oaf_message (id INT AUTO_INCREMENT NOT NULL, text VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE oaf_message_delivery ADD CONSTRAINT FK_571ED908537A1329 FOREIGN KEY (message_id) REFERENCES oaf_message (id)');
        $this->addSql('ALTER TABLE oaf_message_delivery ADD CONSTRAINT FK_571ED908A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE oaf_message_delivery DROP FOREIGN KEY FK_571ED908537A1329');
        $this->addSql('DROP TABLE oaf_message_delivery');
        $this->addSql('DROP TABLE oaf_message');
    }
}
