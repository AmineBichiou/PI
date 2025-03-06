<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250306094944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commentaire_event (id INT AUTO_INCREMENT NOT NULL, user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', evenement_id INT NOT NULL, contenu LONGTEXT NOT NULL, date_creation DATETIME NOT NULL, INDEX IDX_BE22C2BBA76ED395 (user_id), INDEX IDX_BE22C2BBFD02F13 (evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inscription (id INT AUTO_INCREMENT NOT NULL, user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', evenement_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, num_tel VARCHAR(20) DEFAULT NULL, travail VARCHAR(255) NOT NULL, date_inscription DATETIME NOT NULL, INDEX IDX_5E90F6D6A76ED395 (user_id), INDEX IDX_5E90F6D6FD02F13 (evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock_entrepot (stock_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', entrepot_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_97C34318DCD6110 (stock_id), INDEX IDX_97C3431872831E97 (entrepot_id), PRIMARY KEY(stock_id, entrepot_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaire_event ADD CONSTRAINT FK_BE22C2BBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commentaire_event ADD CONSTRAINT FK_BE22C2BBFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE stock_entrepot ADD CONSTRAINT FK_97C34318DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stock_entrepot ADD CONSTRAINT FK_97C3431872831E97 FOREIGN KEY (entrepot_id) REFERENCES entrepot (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE entrepot DROP FOREIGN KEY FK_D805175ADCD6110');
        $this->addSql('DROP INDEX IDX_D805175ADCD6110 ON entrepot');
        $this->addSql('ALTER TABLE entrepot ADD latitude DOUBLE PRECISION DEFAULT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL, DROP stock_id');
        $this->addSql('ALTER TABLE stock CHANGE quantite seuil_alert INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire_event DROP FOREIGN KEY FK_BE22C2BBA76ED395');
        $this->addSql('ALTER TABLE commentaire_event DROP FOREIGN KEY FK_BE22C2BBFD02F13');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6A76ED395');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6FD02F13');
        $this->addSql('ALTER TABLE stock_entrepot DROP FOREIGN KEY FK_97C34318DCD6110');
        $this->addSql('ALTER TABLE stock_entrepot DROP FOREIGN KEY FK_97C3431872831E97');
        $this->addSql('DROP TABLE commentaire_event');
        $this->addSql('DROP TABLE inscription');
        $this->addSql('DROP TABLE stock_entrepot');
        $this->addSql('ALTER TABLE entrepot ADD stock_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', DROP latitude, DROP longitude');
        $this->addSql('ALTER TABLE entrepot ADD CONSTRAINT FK_D805175ADCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('CREATE INDEX IDX_D805175ADCD6110 ON entrepot (stock_id)');
        $this->addSql('ALTER TABLE stock CHANGE seuil_alert quantite INT NOT NULL');
    }
}
