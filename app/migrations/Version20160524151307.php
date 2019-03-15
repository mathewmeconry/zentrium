<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20160524151307 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users CHANGE name last_name VARCHAR(50) NOT NULL, CHANGE firstname first_name VARCHAR(50) NOT NULL, ADD mobile_phone VARCHAR(35) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users CHANGE last_name name VARCHAR(50) NOT NULL, CHANGE first_name firstname VARCHAR(50) NOT NULL, DROP mobile_phone');
    }
}
