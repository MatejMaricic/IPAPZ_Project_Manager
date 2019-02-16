<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190216110731 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE project_developer (project_id INT NOT NULL, developer_id INT NOT NULL, INDEX IDX_74C7CE4D166D1F9C (project_id), INDEX IDX_74C7CE4D64DD9267 (developer_id), PRIMARY KEY(project_id, developer_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_project_status (project_id INT NOT NULL, project_status_id INT NOT NULL, INDEX IDX_8513BAF4166D1F9C (project_id), INDEX IDX_8513BAF47ACB456A (project_status_id), PRIMARY KEY(project_id, project_status_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_developer ADD CONSTRAINT FK_74C7CE4D166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_developer ADD CONSTRAINT FK_74C7CE4D64DD9267 FOREIGN KEY (developer_id) REFERENCES developer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_project_status ADD CONSTRAINT FK_8513BAF4166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_project_status ADD CONSTRAINT FK_8513BAF47ACB456A FOREIGN KEY (project_status_id) REFERENCES project_status (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task ADD project_id INT NOT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_527EDB25166D1F9C ON task (project_id)');
        $this->addSql('ALTER TABLE project ADD name VARCHAR(255) NOT NULL, ADD createdat DATETIME NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE project_developer');
        $this->addSql('DROP TABLE project_project_status');
        $this->addSql('ALTER TABLE project DROP name, DROP createdat');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25166D1F9C');
        $this->addSql('DROP INDEX IDX_527EDB25166D1F9C ON task');
        $this->addSql('ALTER TABLE task DROP project_id');
    }
}
