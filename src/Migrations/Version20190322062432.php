<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190322062432 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE transactions (id INT AUTO_INCREMENT NOT NULL, transaction_id VARCHAR(255) NOT NULL, currency VARCHAR(255) NOT NULL, amount INT NOT NULL, buyer_email VARCHAR(255) NOT NULL, bought_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, createdat DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hours_on_task (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, task_id INT DEFAULT NULL, project_id INT DEFAULT NULL, message LONGTEXT DEFAULT NULL, billable TINYINT(1) DEFAULT NULL, added_at DATETIME NOT NULL, hours INT NOT NULL, INDEX IDX_BFDA07A3A76ED395 (user_id), INDEX IDX_BFDA07A38DB60186 (task_id), INDEX IDX_BFDA07A3166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, task_id INT DEFAULT NULL, user_id INT NOT NULL, discussion_id INT DEFAULT NULL, content LONGTEXT NOT NULL, images LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_5F9E962A8DB60186 (task_id), INDEX IDX_5F9E962AA76ED395 (user_id), INDEX IDX_5F9E962A1ADED311 (discussion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, avatar VARCHAR(255) DEFAULT NULL, full_name VARCHAR(255) NOT NULL, added_by INT NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_project (user_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_77BECEE4A76ED395 (user_id), INDEX IDX_77BECEE4166D1F9C (project_id), PRIMARY KEY(user_id, project_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_task (user_id INT NOT NULL, task_id INT NOT NULL, INDEX IDX_28FF97ECA76ED395 (user_id), INDEX IDX_28FF97EC8DB60186 (task_id), PRIMARY KEY(user_id, task_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, collaboration_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, createdat DATETIME NOT NULL, deadline DATETIME NOT NULL, completed TINYINT(1) DEFAULT NULL, INDEX IDX_2FB3D0EEEF1544CE (collaboration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_project_status (project_id INT NOT NULL, project_status_id INT NOT NULL, INDEX IDX_8513BAF4166D1F9C (project_id), INDEX IDX_8513BAF47ACB456A (project_status_id), PRIMARY KEY(project_id, project_status_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, status_id INT NOT NULL, name VARCHAR(255) NOT NULL, createdat DATETIME NOT NULL, content LONGTEXT NOT NULL, images LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', priority VARCHAR(255) NOT NULL, completed TINYINT(1) NOT NULL, subscribed LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', estimate INT DEFAULT NULL, total_hours INT DEFAULT NULL, updated TINYINT(1) DEFAULT NULL, INDEX IDX_527EDB25166D1F9C (project_id), INDEX IDX_527EDB256BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discussion (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_C0B9F90F166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE collaboration (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, created_at DATETIME NOT NULL, subscribed TINYINT(1) NOT NULL, subscribed_until DATETIME NOT NULL, UNIQUE INDEX UNIQ_DA3AE323A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscriptions (id INT AUTO_INCREMENT NOT NULL, user_email VARCHAR(255) NOT NULL, task_id INT DEFAULT NULL, discussion_id INT DEFAULT NULL, UNIQUE INDEX subscriptions (user_email, discussion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hours_on_task ADD CONSTRAINT FK_BFDA07A3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE hours_on_task ADD CONSTRAINT FK_BFDA07A38DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE hours_on_task ADD CONSTRAINT FK_BFDA07A3166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A8DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A1ADED311 FOREIGN KEY (discussion_id) REFERENCES discussion (id)');
        $this->addSql('ALTER TABLE user_project ADD CONSTRAINT FK_77BECEE4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_project ADD CONSTRAINT FK_77BECEE4166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_task ADD CONSTRAINT FK_28FF97ECA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_task ADD CONSTRAINT FK_28FF97EC8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEEF1544CE FOREIGN KEY (collaboration_id) REFERENCES collaboration (id)');
        $this->addSql('ALTER TABLE project_project_status ADD CONSTRAINT FK_8513BAF4166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_project_status ADD CONSTRAINT FK_8513BAF47ACB456A FOREIGN KEY (project_status_id) REFERENCES project_status (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB256BF700BD FOREIGN KEY (status_id) REFERENCES project_status (id)');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE collaboration ADD CONSTRAINT FK_DA3AE323A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE project_project_status DROP FOREIGN KEY FK_8513BAF47ACB456A');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB256BF700BD');
        $this->addSql('ALTER TABLE hours_on_task DROP FOREIGN KEY FK_BFDA07A3A76ED395');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AA76ED395');
        $this->addSql('ALTER TABLE user_project DROP FOREIGN KEY FK_77BECEE4A76ED395');
        $this->addSql('ALTER TABLE user_task DROP FOREIGN KEY FK_28FF97ECA76ED395');
        $this->addSql('ALTER TABLE collaboration DROP FOREIGN KEY FK_DA3AE323A76ED395');
        $this->addSql('ALTER TABLE hours_on_task DROP FOREIGN KEY FK_BFDA07A3166D1F9C');
        $this->addSql('ALTER TABLE user_project DROP FOREIGN KEY FK_77BECEE4166D1F9C');
        $this->addSql('ALTER TABLE project_project_status DROP FOREIGN KEY FK_8513BAF4166D1F9C');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25166D1F9C');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F166D1F9C');
        $this->addSql('ALTER TABLE hours_on_task DROP FOREIGN KEY FK_BFDA07A38DB60186');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A8DB60186');
        $this->addSql('ALTER TABLE user_task DROP FOREIGN KEY FK_28FF97EC8DB60186');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A1ADED311');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEEF1544CE');
        $this->addSql('DROP TABLE transactions');
        $this->addSql('DROP TABLE project_status');
        $this->addSql('DROP TABLE hours_on_task');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_project');
        $this->addSql('DROP TABLE user_task');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_project_status');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE discussion');
        $this->addSql('DROP TABLE collaboration');
        $this->addSql('DROP TABLE subscriptions');
    }
}
