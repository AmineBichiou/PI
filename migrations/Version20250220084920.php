<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220084920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stock_entrepot (stock_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', entrepot_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_97C34318DCD6110 (stock_id), INDEX IDX_97C3431872831E97 (entrepot_id), PRIMARY KEY(stock_id, entrepot_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stock_entrepot ADD CONSTRAINT FK_97C34318DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stock_entrepot ADD CONSTRAINT FK_97C3431872831E97 FOREIGN KEY (entrepot_id) REFERENCES entrepot (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DF347EFB');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395');
        $this->addSql('ALTER TABLE commande_finalisee DROP FOREIGN KEY FK_67F018ECA76ED395');
        $this->addSql('ALTER TABLE evenement_region DROP FOREIGN KEY FK_BC9721F498260155');
        $this->addSql('ALTER TABLE evenement_region DROP FOREIGN KEY FK_BC9721F4FD02F13');
        $this->addSql('ALTER TABLE message_reclamation DROP FOREIGN KEY FK_AE1F3AFD2D6BA2D9');
        $this->addSql('ALTER TABLE message_reclamation DROP FOREIGN KEY FK_AE1F3AFDA76ED395');
        $this->addSql('ALTER TABLE reclamations DROP FOREIGN KEY FK_1CAD6B76A76ED395');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE commande_finalisee');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE evenement_region');
        $this->addSql('DROP TABLE message_reclamation');
        $this->addSql('DROP TABLE reclamations');
        $this->addSql('DROP TABLE region');
        $this->addSql('ALTER TABLE categorie DROP nom, DROP description, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE entrepot DROP FOREIGN KEY FK_D805175ADCD6110');
        $this->addSql('DROP INDEX IDX_D805175ADCD6110 ON entrepot');
        $this->addSql('ALTER TABLE entrepot DROP stock_id');
        $this->addSql('ALTER TABLE produit CHANGE categorie_id categorie_id INT NOT NULL');
        $this->addSql('ALTER TABLE reset_password_request CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', produit_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', quantite INT NOT NULL, date_commande DATETIME NOT NULL, INDEX IDX_6EEAA67DF347EFB (produit_id), INDEX IDX_6EEAA67DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE commande_finalisee (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', nom_produit VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, quantite INT NOT NULL, prix_total NUMERIC(10, 2) NOT NULL, date_commande DATETIME NOT NULL, produit_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', produit_prix NUMERIC(10, 2) NOT NULL, INDEX IDX_67F018ECA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, type VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, statut VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, photo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evenement_region (id INT AUTO_INCREMENT NOT NULL, evenement_id INT NOT NULL, region_id INT NOT NULL, INDEX IDX_BC9721F4FD02F13 (evenement_id), INDEX IDX_BC9721F498260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE message_reclamation (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', reclamation_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', contenu VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_message DATETIME NOT NULL, INDEX IDX_AE1F3AFDA76ED395 (user_id), INDEX IDX_AE1F3AFD2D6BA2D9 (reclamation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reclamations (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', date_reclamation DATETIME NOT NULL, rate INT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, statut VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_1CAD6B76A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ville VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commande_finalisee ADD CONSTRAINT FK_67F018ECA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE evenement_region ADD CONSTRAINT FK_BC9721F498260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE evenement_region ADD CONSTRAINT FK_BC9721F4FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE message_reclamation ADD CONSTRAINT FK_AE1F3AFD2D6BA2D9 FOREIGN KEY (reclamation_id) REFERENCES reclamations (id)');
        $this->addSql('ALTER TABLE message_reclamation ADD CONSTRAINT FK_AE1F3AFDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reclamations ADD CONSTRAINT FK_1CAD6B76A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stock_entrepot DROP FOREIGN KEY FK_97C34318DCD6110');
        $this->addSql('ALTER TABLE stock_entrepot DROP FOREIGN KEY FK_97C3431872831E97');
        $this->addSql('DROP TABLE stock_entrepot');
        $this->addSql('ALTER TABLE categorie ADD nom VARCHAR(255) NOT NULL, ADD description LONGTEXT DEFAULT NULL, CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE entrepot ADD stock_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE entrepot ADD CONSTRAINT FK_D805175ADCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('CREATE INDEX IDX_D805175ADCD6110 ON entrepot (stock_id)');
        $this->addSql('ALTER TABLE produit CHANGE categorie_id categorie_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE reset_password_request CHANGE id id VARCHAR(255) NOT NULL, CHANGE user_id user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE user CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
    }
}
