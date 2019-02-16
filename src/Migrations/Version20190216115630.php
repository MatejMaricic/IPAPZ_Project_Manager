<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190216115630 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE developer_project DROP FOREIGN KEY FK_412D364564DD9267');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE60984F51');
        $this->addSql('DROP TABLE developer');
        $this->addSql('DROP TABLE developer_project');
        $this->addSql('DROP TABLE project_manager');
        $this->addSql('DROP INDEX IDX_2FB3D0EE60984F51 ON project');
        $this->addSql('ALTER TABLE project DROP project_manager_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE developer (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, lastname VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, email VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, createdat VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE developer_project (developer_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_412D3645166D1F9C (project_id), INDEX IDX_412D364564DD9267 (developer_id), PRIMARY KEY(developer_id, project_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE project_manager (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, lastname VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, createdat DATETIME NOT NULL, email VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE developer_project ADD CONSTRAINT FK_412D3645166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE developer_project ADD CONSTRAINT FK_412D364564DD9267 FOREIGN KEY (developer_id) REFERENCES developer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project ADD project_manager_id INT NOT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE60984F51 FOREIGN KEY (project_manager_id) REFERENCES project_manager (id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE60984F51 ON project (project_manager_id)');
    }
}
