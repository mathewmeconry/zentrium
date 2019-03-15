<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20160527222953 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE requirement (id INT AUTO_INCREMENT NOT NULL, set_id INT NOT NULL, task_id INT NOT NULL, `from` DATETIME NOT NULL, `to` DATETIME NOT NULL, count INT NOT NULL, notes VARCHAR(255) DEFAULT NULL, INDEX IDX_DB3F555010FB0D18 (set_id), INDEX IDX_DB3F55508DB60186 (task_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, notes VARCHAR(255) DEFAULT NULL, color VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_527EDB2577153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE requirement_set (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, begin DATETIME NOT NULL, end DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE requirement ADD CONSTRAINT FK_DB3F555010FB0D18 FOREIGN KEY (set_id) REFERENCES requirement_set (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE requirement ADD CONSTRAINT FK_DB3F55508DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE requirement DROP FOREIGN KEY FK_DB3F55508DB60186');
        $this->addSql('ALTER TABLE requirement DROP FOREIGN KEY FK_DB3F555010FB0D18');
        $this->addSql('DROP TABLE requirement');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE requirement_set');
    }
}
