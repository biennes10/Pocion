<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200212090337 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE agenda (id INT AUTO_INCREMENT NOT NULL, link_id INT DEFAULT NULL, project_id INT DEFAULT NULL, user_id INT NOT NULL, created_at DATETIME NOT NULL, status INT NOT NULL, start_at DATETIME DEFAULT NULL, end_at DATETIME DEFAULT NULL, subject VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX IDX_2CEDC877ADA40271 (link_id), INDEX IDX_2CEDC877166D1F9C (project_id), INDEX IDX_2CEDC877A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, project_id INT DEFAULT NULL, link_id INT DEFAULT NULL, created_at DATETIME NOT NULL, status INT NOT NULL, validation INT NOT NULL, url VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, INDEX IDX_8C9F3610A76ED395 (user_id), INDEX IDX_8C9F3610166D1F9C (project_id), INDEX IDX_8C9F3610ADA40271 (link_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE handrail (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, link_id INT DEFAULT NULL, project_id INT DEFAULT NULL, created_at DATETIME NOT NULL, status INT NOT NULL, type INT NOT NULL, urgency INT NOT NULL, subject VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX IDX_6DB9A80CA76ED395 (user_id), INDEX IDX_6DB9A80CADA40271 (link_id), INDEX IDX_6DB9A80C166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, data VARCHAR(255) DEFAULT NULL, icon VARCHAR(255) NOT NULL, path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notif_user (id INT AUTO_INCREMENT NOT NULL, notification_id INT NOT NULL, user_id INT NOT NULL, opened TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_51733994EF1A9D84 (notification_id), INDEX IDX_51733994A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, name VARCHAR(255) NOT NULL, status INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_2FB3D0EEF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_user (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, project_id INT DEFAULT NULL, INDEX IDX_B4021E51A76ED395 (user_id), INDEX IDX_B4021E51166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, gender INT NOT NULL, date_of_birth DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, roles JSON NOT NULL, reset_token VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE agenda ADD CONSTRAINT FK_2CEDC877ADA40271 FOREIGN KEY (link_id) REFERENCES link (id)');
        $this->addSql('ALTER TABLE agenda ADD CONSTRAINT FK_2CEDC877166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE agenda ADD CONSTRAINT FK_2CEDC877A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610ADA40271 FOREIGN KEY (link_id) REFERENCES link (id)');
        $this->addSql('ALTER TABLE handrail ADD CONSTRAINT FK_6DB9A80CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE handrail ADD CONSTRAINT FK_6DB9A80CADA40271 FOREIGN KEY (link_id) REFERENCES link (id)');
        $this->addSql('ALTER TABLE handrail ADD CONSTRAINT FK_6DB9A80C166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE notif_user ADD CONSTRAINT FK_51733994EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id)');
        $this->addSql('ALTER TABLE notif_user ADD CONSTRAINT FK_51733994A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE agenda DROP FOREIGN KEY FK_2CEDC877ADA40271');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610ADA40271');
        $this->addSql('ALTER TABLE handrail DROP FOREIGN KEY FK_6DB9A80CADA40271');
        $this->addSql('ALTER TABLE notif_user DROP FOREIGN KEY FK_51733994EF1A9D84');
        $this->addSql('ALTER TABLE agenda DROP FOREIGN KEY FK_2CEDC877166D1F9C');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610166D1F9C');
        $this->addSql('ALTER TABLE handrail DROP FOREIGN KEY FK_6DB9A80C166D1F9C');
        $this->addSql('ALTER TABLE project_user DROP FOREIGN KEY FK_B4021E51166D1F9C');
        $this->addSql('ALTER TABLE agenda DROP FOREIGN KEY FK_2CEDC877A76ED395');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610A76ED395');
        $this->addSql('ALTER TABLE handrail DROP FOREIGN KEY FK_6DB9A80CA76ED395');
        $this->addSql('ALTER TABLE notif_user DROP FOREIGN KEY FK_51733994A76ED395');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEF675F31B');
        $this->addSql('ALTER TABLE project_user DROP FOREIGN KEY FK_B4021E51A76ED395');
        $this->addSql('DROP TABLE agenda');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE handrail');
        $this->addSql('DROP TABLE link');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE notif_user');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_user');
        $this->addSql('DROP TABLE user');
    }
}
