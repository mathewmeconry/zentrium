<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160703204200 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE log_comments (id INT AUTO_INCREMENT NOT NULL, log_id INT DEFAULT NULL, author_id INT NOT NULL, message LONGTEXT NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_31CD5DF5EA675D86 (log_id), INDEX IDX_31CD5DF5F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logs (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(100) NOT NULL, details LONGTEXT DEFAULT NULL, status VARCHAR(20) NOT NULL, edited DATETIME NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_F08FC65CF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log_label (log_id INT NOT NULL, label_id INT NOT NULL, INDEX IDX_2ED9BBB5EA675D86 (log_id), INDEX IDX_2ED9BBB533B92F39 (label_id), PRIMARY KEY(log_id, label_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log_labels (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, color VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE log_comments ADD CONSTRAINT FK_31CD5DF5EA675D86 FOREIGN KEY (log_id) REFERENCES logs (id)');
        $this->addSql('ALTER TABLE log_comments ADD CONSTRAINT FK_31CD5DF5F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE logs ADD CONSTRAINT FK_F08FC65CF675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE log_label ADD CONSTRAINT FK_2ED9BBB5EA675D86 FOREIGN KEY (log_id) REFERENCES logs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE log_label ADD CONSTRAINT FK_2ED9BBB533B92F39 FOREIGN KEY (label_id) REFERENCES log_labels (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE log_comments DROP FOREIGN KEY FK_31CD5DF5EA675D86');
        $this->addSql('ALTER TABLE log_label DROP FOREIGN KEY FK_2ED9BBB5EA675D86');
        $this->addSql('ALTER TABLE log_label DROP FOREIGN KEY FK_2ED9BBB533B92F39');
        $this->addSql('DROP TABLE log_comments');
        $this->addSql('DROP TABLE logs');
        $this->addSql('DROP TABLE log_label');
        $this->addSql('DROP TABLE log_labels');
    }
}
