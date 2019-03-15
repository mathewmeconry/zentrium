<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20160516163637 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE logs (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) NOT NULL, status VARCHAR(20) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log_label (log_id INT NOT NULL, label_id INT NOT NULL, INDEX IDX_2ED9BBB5EA675D86 (log_id), INDEX IDX_2ED9BBB533B92F39 (label_id), PRIMARY KEY(log_id, label_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log_labels (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, color VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE log_label ADD CONSTRAINT FK_2ED9BBB5EA675D86 FOREIGN KEY (log_id) REFERENCES logs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE log_label ADD CONSTRAINT FK_2ED9BBB533B92F39 FOREIGN KEY (label_id) REFERENCES log_labels (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE log_label DROP FOREIGN KEY FK_2ED9BBB5EA675D86');
        $this->addSql('ALTER TABLE log_label DROP FOREIGN KEY FK_2ED9BBB533B92F39');
        $this->addSql('DROP TABLE logs');
        $this->addSql('DROP TABLE log_label');
        $this->addSql('DROP TABLE log_labels');
    }
}
