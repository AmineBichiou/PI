<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250228223217 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE inscription (id INT AUTO_INCREMENT NOT NULL, user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', evenement_id INT NOT NULL, date_inscription DATETIME NOT NULL, INDEX IDX_5E90F6D6A76ED395 (user_id), INDEX IDX_5E90F6D6FD02F13 (evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE evenement ADD nb_participants INT NOT NULL, DROP type, DROP statut, DROP photo, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE date_fin date_fin DATETIME DEFAULT NULL, CHANGE titre nom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE num_tel num_tel VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6A76ED395');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6FD02F13');
        $this->addSql('DROP TABLE inscription');
        $this->addSql('ALTER TABLE evenement ADD type VARCHAR(20) NOT NULL, ADD statut VARCHAR(20) NOT NULL, ADD photo VARCHAR(255) DEFAULT NULL, DROP nb_participants, CHANGE description description LONGTEXT NOT NULL, CHANGE date_fin date_fin DATETIME NOT NULL, CHANGE nom titre VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE num_tel num_tel INT NOT NULL');
    }
}
