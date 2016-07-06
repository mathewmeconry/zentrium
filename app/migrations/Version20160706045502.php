<?php

namespace Zentrium\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160706045502 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE oaf_resource_assignment (id INT AUTO_INCREMENT NOT NULL, resource_id INT NOT NULL, user_id INT NOT NULL, assigned_by_id INT NOT NULL, returned_by_id INT DEFAULT NULL, assigned_at DATETIME NOT NULL, returned_at DATETIME DEFAULT NULL, INDEX IDX_E016E30E89329D25 (resource_id), INDEX IDX_E016E30EA76ED395 (user_id), INDEX IDX_E016E30E6E6F1246 (assigned_by_id), INDEX IDX_E016E30E71AD87D9 (returned_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oaf_resource (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, `label` VARCHAR(20) NOT NULL, INDEX IDX_18E8AD017E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE oaf_resource_assignment ADD CONSTRAINT FK_E016E30E89329D25 FOREIGN KEY (resource_id) REFERENCES oaf_resource (id)');
        $this->addSql('ALTER TABLE oaf_resource_assignment ADD CONSTRAINT FK_E016E30EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE oaf_resource_assignment ADD CONSTRAINT FK_E016E30E6E6F1246 FOREIGN KEY (assigned_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE oaf_resource_assignment ADD CONSTRAINT FK_E016E30E71AD87D9 FOREIGN KEY (returned_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE oaf_resource ADD CONSTRAINT FK_18E8AD017E3C61F9 FOREIGN KEY (owner_id) REFERENCES groups (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE oaf_resource_assignment DROP FOREIGN KEY FK_E016E30E89329D25');
        $this->addSql('DROP TABLE oaf_resource_assignment');
        $this->addSql('DROP TABLE oaf_resource');
    }
}
