<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20160615222404 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE schedule_constraint (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, parameters LONGTEXT NOT NULL, parameters_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE schedule_default_constraint (schedule_id INT NOT NULL, constraint_id INT NOT NULL, INDEX IDX_BB7E03BDA40BC2D5 (schedule_id), INDEX IDX_BB7E03BDE3087FFC (constraint_id), PRIMARY KEY(schedule_id, constraint_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE schedule_default_constraint ADD CONSTRAINT FK_BB7E03BDA40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE schedule_default_constraint ADD CONSTRAINT FK_BB7E03BDE3087FFC FOREIGN KEY (constraint_id) REFERENCES schedule_constraint (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE schedule_default_constraint DROP FOREIGN KEY FK_BB7E03BDE3087FFC');
        $this->addSql('DROP TABLE schedule_constraint');
        $this->addSql('DROP TABLE schedule_default_constraint');
    }
}
