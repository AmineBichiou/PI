<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221184754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE entrepot (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', stock_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, ville VARCHAR(255) DEFAULT NULL, espace DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_D805175A6C6E55B5 (nom), INDEX IDX_D805175ADCD6110 (stock_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fournisseur (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, telephone VARCHAR(20) NOT NULL, email VARCHAR(180) NOT NULL, actif VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fournisseur_stock (fournisseur_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', stock_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_5660E395670C757F (fournisseur_id), INDEX IDX_5660E395DCD6110 (stock_id), PRIMARY KEY(fournisseur_id, stock_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', produit_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', quantite INT NOT NULL, date_entree DATETIME NOT NULL, date_sortie DATETIME DEFAULT NULL, INDEX IDX_4B365660F347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE entrepot ADD CONSTRAINT FK_D805175ADCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('ALTER TABLE fournisseur_stock ADD CONSTRAINT FK_5660E395670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fournisseur_stock ADD CONSTRAINT FK_5660E395DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE commande_finalisee DROP FOREIGN KEY FK_67F018EC82EA2E54');
        $this->addSql('DROP INDEX IDX_67F018EC82EA2E54 ON commande_finalisee');
        $this->addSql('ALTER TABLE commande_finalisee ADD produit_prix NUMERIC(10, 2) NOT NULL, CHANGE commande_id produit_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE evenement CHANGE titre titre VARCHAR(255) NOT NULL, CHANGE description description LONGTEXT NOT NULL, CHANGE type type VARCHAR(20) NOT NULL, CHANGE statut statut VARCHAR(20) NOT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL, CHANGE date_debut date_debut DATETIME NOT NULL, CHANGE date_fin date_fin DATETIME NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE evenement_region ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE evenement_region ADD CONSTRAINT FK_BC9721F4FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE evenement_region ADD CONSTRAINT FK_BC9721F498260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('CREATE INDEX IDX_BC9721F4FD02F13 ON evenement_region (evenement_id)');
        $this->addSql('CREATE INDEX IDX_BC9721F498260155 ON evenement_region (region_id)');
        $this->addSql('ALTER TABLE produit ADD user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD quantite INT NOT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_29A5EC27A76ED395 ON produit (user_id)');
        $this->addSql('ALTER TABLE region CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE ville ville VARCHAR(255) NOT NULL, CHANGE description description VARCHAR(255) NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE body body LONGTEXT NOT NULL, CHANGE headers headers LONGTEXT NOT NULL, CHANGE queue_name queue_name VARCHAR(190) NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entrepot DROP FOREIGN KEY FK_D805175ADCD6110');
        $this->addSql('ALTER TABLE fournisseur_stock DROP FOREIGN KEY FK_5660E395670C757F');
        $this->addSql('ALTER TABLE fournisseur_stock DROP FOREIGN KEY FK_5660E395DCD6110');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660F347EFB');
        $this->addSql('DROP TABLE entrepot');
        $this->addSql('DROP TABLE fournisseur');
        $this->addSql('DROP TABLE fournisseur_stock');
        $this->addSql('DROP TABLE stock');
        $this->addSql('ALTER TABLE commande_finalisee DROP produit_prix, CHANGE produit_id commande_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE commande_finalisee ADD CONSTRAINT FK_67F018EC82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_67F018EC82EA2E54 ON commande_finalisee (commande_id)');
        $this->addSql('ALTER TABLE evenement MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON evenement');
        $this->addSql('ALTER TABLE evenement CHANGE titre titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE type type VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE statut statut VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE date_debut date_debut DATE NOT NULL, CHANGE date_fin date_fin DATE NOT NULL, CHANGE photo photo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE evenement_region MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE evenement_region DROP FOREIGN KEY FK_BC9721F4FD02F13');
        $this->addSql('ALTER TABLE evenement_region DROP FOREIGN KEY FK_BC9721F498260155');
        $this->addSql('DROP INDEX IDX_BC9721F4FD02F13 ON evenement_region');
        $this->addSql('DROP INDEX IDX_BC9721F498260155 ON evenement_region');
        $this->addSql('DROP INDEX `primary` ON evenement_region');
        $this->addSql('ALTER TABLE messenger_messages MODIFY id BIGINT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0 ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E0E3BD61CE ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E016BA31DB ON messenger_messages');
        $this->addSql('ALTER TABLE messenger_messages CHANGE body body LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE headers headers LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE queue_name queue_name VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27A76ED395');
        $this->addSql('DROP INDEX IDX_29A5EC27A76ED395 ON produit');
        $this->addSql('ALTER TABLE produit DROP user_id, DROP quantite');
        $this->addSql('ALTER TABLE region MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON region');
        $this->addSql('ALTER TABLE region CHANGE nom nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE ville ville VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
