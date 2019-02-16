<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190216113747 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE task_status_task (task_status_id INT NOT NULL, task_id INT NOT NULL, INDEX IDX_2C0DDB8314DDCDEC (task_status_id), INDEX IDX_2C0DDB838DB60186 (task_id), PRIMARY KEY(task_status_id, task_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE task_status_task ADD CONSTRAINT FK_2C0DDB8314DDCDEC FOREIGN KEY (task_status_id) REFERENCES task_status (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_status_task ADD CONSTRAINT FK_2C0DDB838DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task ADD name VARCHAR(255) NOT NULL, ADD createdat DATETIME NOT NULL');
        $this->addSql('ALTER TABLE task_status ADD name VARCHAR(255) NOT NULL, ADD createdat DATETIME NOT NULL');
        $this->addSql('ALTER TABLE project_status ADD name VARCHAR(255) NOT NULL, ADD createdat DATETIME NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE task_status_task');
        $this->addSql('ALTER TABLE project_status DROP name, DROP createdat');
        $this->addSql('ALTER TABLE task DROP name, DROP createdat');
        $this->addSql('ALTER TABLE task_status DROP name, DROP createdat');
    }
}
