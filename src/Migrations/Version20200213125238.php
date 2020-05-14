<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200213125238 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notif_user ADD sent_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notif_user ADD CONSTRAINT FK_51733994A45BB98C FOREIGN KEY (sent_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_51733994A45BB98C ON notif_user (sent_by_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notif_user DROP FOREIGN KEY FK_51733994A45BB98C');
        $this->addSql('DROP INDEX IDX_51733994A45BB98C ON notif_user');
        $this->addSql('ALTER TABLE notif_user DROP sent_by_id');
    }
}
