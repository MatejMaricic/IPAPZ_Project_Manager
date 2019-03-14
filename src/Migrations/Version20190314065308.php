<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190314065308 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comments ADD discussion_id INT NOT NULL');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A1ADED311 FOREIGN KEY (discussion_id) REFERENCES discussion (id)');
        $this->addSql('CREATE INDEX IDX_5F9E962A1ADED311 ON comments (discussion_id)');
        $this->addSql('ALTER TABLE discussion ADD content LONGTEXT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A1ADED311');
        $this->addSql('DROP INDEX IDX_5F9E962A1ADED311 ON comments');
        $this->addSql('ALTER TABLE comments DROP discussion_id');
        $this->addSql('ALTER TABLE discussion DROP content');
    }
}
