<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20170702160033 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE log_comments DROP FOREIGN KEY FK_31CD5DF5EA675D86');
        $this->addSql('ALTER TABLE log_label DROP FOREIGN KEY FK_2ED9BBB5EA675D86');
        $this->addSql('ALTER TABLE log_comments CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE log_id log_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE logs CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE log_label CHANGE log_id log_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE log_comments ADD CONSTRAINT FK_31CD5DF5EA675D86 FOREIGN KEY (log_id) REFERENCES logs (id)');
        $this->addSql('ALTER TABLE log_label ADD CONSTRAINT FK_2ED9BBB5EA675D86 FOREIGN KEY (log_id) REFERENCES logs (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE log_comments DROP FOREIGN KEY FK_31CD5DF5EA675D86');
        $this->addSql('ALTER TABLE log_label DROP FOREIGN KEY FK_2ED9BBB5EA675D86');
        $this->addSql('ALTER TABLE log_comments CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE log_id log_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE log_label CHANGE log_id log_id INT NOT NULL');
        $this->addSql('ALTER TABLE logs CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE log_comments ADD CONSTRAINT FK_31CD5DF5EA675D86 FOREIGN KEY (log_id) REFERENCES logs (id)');
        $this->addSql('ALTER TABLE log_label ADD CONSTRAINT FK_2ED9BBB5EA675D86 FOREIGN KEY (log_id) REFERENCES logs (id) ON DELETE CASCADE');
    }
}
