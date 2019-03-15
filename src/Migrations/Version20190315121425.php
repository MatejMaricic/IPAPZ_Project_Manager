<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190315121425 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE hours_on_task ADD project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE hours_on_task ADD CONSTRAINT FK_BFDA07A3166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_BFDA07A3166D1F9C ON hours_on_task (project_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE hours_on_task DROP FOREIGN KEY FK_BFDA07A3166D1F9C');
        $this->addSql('DROP INDEX IDX_BFDA07A3166D1F9C ON hours_on_task');
        $this->addSql('ALTER TABLE hours_on_task DROP project_id');
    }
}
