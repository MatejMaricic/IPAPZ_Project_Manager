<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190216112739 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE developer_project (developer_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_412D364564DD9267 (developer_id), INDEX IDX_412D3645166D1F9C (project_id), PRIMARY KEY(developer_id, project_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE developer_project ADD CONSTRAINT FK_412D364564DD9267 FOREIGN KEY (developer_id) REFERENCES developer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE developer_project ADD CONSTRAINT FK_412D3645166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE project_developer');
        $this->addSql('ALTER TABLE project_manager ADD email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE developer ADD firstname VARCHAR(255) NOT NULL, ADD lastname VARCHAR(255) NOT NULL, ADD email VARCHAR(255) NOT NULL, ADD createdat VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE project_developer (project_id INT NOT NULL, developer_id INT NOT NULL, INDEX IDX_74C7CE4D64DD9267 (developer_id), INDEX IDX_74C7CE4D166D1F9C (project_id), PRIMARY KEY(project_id, developer_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE project_developer ADD CONSTRAINT FK_74C7CE4D166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_developer ADD CONSTRAINT FK_74C7CE4D64DD9267 FOREIGN KEY (developer_id) REFERENCES developer (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE developer_project');
        $this->addSql('ALTER TABLE developer DROP firstname, DROP lastname, DROP email, DROP createdat');
        $this->addSql('ALTER TABLE project_manager DROP email');
    }
}
