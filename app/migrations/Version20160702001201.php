<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160702001201 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE log_comments (id INT AUTO_INCREMENT NOT NULL, log_id INT DEFAULT NULL, author_id INT NOT NULL, message LONGTEXT NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_31CD5DF5EA675D86 (log_id), INDEX IDX_31CD5DF5F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE log_comments ADD CONSTRAINT FK_31CD5DF5EA675D86 FOREIGN KEY (log_id) REFERENCES logs (id)');
        $this->addSql('ALTER TABLE log_comments ADD CONSTRAINT FK_31CD5DF5F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE logs ADD author_id INT NOT NULL, ADD details LONGTEXT DEFAULT NULL, ADD edited DATETIME NOT NULL');
        $this->addSql('ALTER TABLE logs ADD CONSTRAINT FK_F08FC65CF675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_F08FC65CF675F31B ON logs (author_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE log_comments');
        $this->addSql('ALTER TABLE logs DROP FOREIGN KEY FK_F08FC65CF675F31B');
        $this->addSql('DROP INDEX IDX_F08FC65CF675F31B ON logs');
        $this->addSql('ALTER TABLE logs DROP author_id, DROP details, DROP edited');
    }
}
