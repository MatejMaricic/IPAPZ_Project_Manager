<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190314093130 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX subscriptions ON subscriptions');
        $this->addSql('ALTER TABLE subscriptions CHANGE task_id task_id INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX subscriptions ON subscriptions (user_email, discussion_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX subscriptions ON subscriptions');
        $this->addSql('ALTER TABLE subscriptions CHANGE task_id task_id INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX subscriptions ON subscriptions (user_email, task_id)');
    }
}
