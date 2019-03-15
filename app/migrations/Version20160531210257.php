<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20160531210257 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE shift (id INT AUTO_INCREMENT NOT NULL, schedule_id INT NOT NULL, task_id INT NOT NULL, user_id INT NOT NULL, from_ DATETIME NOT NULL, to_ DATETIME NOT NULL, INDEX IDX_A50B3B45A40BC2D5 (schedule_id), INDEX IDX_A50B3B458DB60186 (task_id), INDEX IDX_A50B3B45A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE schedule (id INT AUTO_INCREMENT NOT NULL, published TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, begin DATETIME NOT NULL, end DATETIME NOT NULL, slot_duration INT NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shift ADD CONSTRAINT FK_A50B3B45A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shift ADD CONSTRAINT FK_A50B3B458DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shift ADD CONSTRAINT FK_A50B3B45A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE requirement DROP notes');
        $this->addSql('ALTER TABLE requirement_set ADD created DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE shift DROP FOREIGN KEY FK_A50B3B45A40BC2D5');
        $this->addSql('DROP TABLE shift');
        $this->addSql('DROP TABLE schedule');
        $this->addSql('ALTER TABLE requirement ADD notes VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE requirement_set DROP created');
    }
}
