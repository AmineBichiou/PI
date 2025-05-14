<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250514155724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE commandes_historiques (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', user_id VARCHAR(36) NOT NULL, utilisateur BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', date_achat DATETIME NOT NULL, prix_total NUMERIC(10, 2) NOT NULL, INDEX IDX_FFDA3E36A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commandes_historiques ADD CONSTRAINT FK_FFDA3E36A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande_finalisee DROP FOREIGN KEY FK_67F018ECA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE commande_finalisee
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP verification_token
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE commande_finalisee (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', user_id VARCHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, nom_produit VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, quantite INT NOT NULL, prix_total NUMERIC(10, 2) NOT NULL, date_commande DATETIME NOT NULL, produit_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', produit_prix NUMERIC(10, 2) NOT NULL, INDEX IDX_67F018ECA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande_finalisee ADD CONSTRAINT FK_67F018ECA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commandes_historiques DROP FOREIGN KEY FK_FFDA3E36A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE commandes_historiques
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD verification_token VARCHAR(36) DEFAULT NULL
        SQL);
    }
}
