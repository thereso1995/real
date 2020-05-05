<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191130031040 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE profil (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entreprise (id INT AUTO_INCREMENT NOT NULL, raison_sociale VARCHAR(255) NOT NULL, ninea VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, telephone_entreprise VARCHAR(255) DEFAULT NULL, email_entreprise VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, user_compte_partenaire_emetteur_id INT NOT NULL, user_compte_partenaire_recepteur_id INT NOT NULL, nom_client_emetteur VARCHAR(255) NOT NULL, telephone_emetteur VARCHAR(255) NOT NULL, nci_emetteur VARCHAR(255) NOT NULL, date_envoi DATETIME NOT NULL, code VARCHAR(255) NOT NULL, montant BIGINT NOT NULL, frais INT NOT NULL, nom_client_recepteur VARCHAR(255) NOT NULL, telephone_recepteur VARCHAR(255) NOT NULL, nci_recepteur VARCHAR(255) NOT NULL, date_reception DATETIME DEFAULT NULL, commission_emetteur INT NOT NULL, commission_recepteur VARCHAR(255) DEFAULT NULL, commission_wari INT NOT NULL, taxes_etat INT NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_723705D1FB88E14F (utilisateur_id), INDEX IDX_723705D13F3F07EC (user_compte_partenaire_emetteur_id), INDEX IDX_723705D176BDCF5C (user_compte_partenaire_recepteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE depot (id INT AUTO_INCREMENT NOT NULL, caissier_id INT NOT NULL, compte_id INT NOT NULL, date DATETIME NOT NULL, montant BIGINT NOT NULL, INDEX IDX_47948BBCB514973B (caissier_id), INDEX IDX_47948BBCF2C56620 (compte_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tarifs (id INT AUTO_INCREMENT NOT NULL, borne_inferieure INT NOT NULL, borne_superieure INT NOT NULL, valeur INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_compte_actuel (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, compte_id INT NOT NULL, date_affectation DATETIME NOT NULL, INDEX IDX_7206AB47FB88E14F (utilisateur_id), INDEX IDX_7206AB47F2C56620 (compte_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE compte (id INT AUTO_INCREMENT NOT NULL, entreprise_id INT NOT NULL, numero_compte VARCHAR(255) NOT NULL, solde BIGINT DEFAULT NULL, INDEX IDX_CFF65260A4AEAFEA (entreprise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, entreprise_id INT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, nci VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3F85E0677 (username), INDEX IDX_1D1C63B3A4AEAFEA (entreprise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D13F3F07EC FOREIGN KEY (user_compte_partenaire_emetteur_id) REFERENCES user_compte_actuel (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D176BDCF5C FOREIGN KEY (user_compte_partenaire_recepteur_id) REFERENCES user_compte_actuel (id)');
        $this->addSql('ALTER TABLE depot ADD CONSTRAINT FK_47948BBCB514973B FOREIGN KEY (caissier_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE depot ADD CONSTRAINT FK_47948BBCF2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE user_compte_actuel ADD CONSTRAINT FK_7206AB47FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE user_compte_actuel ADD CONSTRAINT FK_7206AB47F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT FK_CFF65260A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE compte DROP FOREIGN KEY FK_CFF65260A4AEAFEA');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3A4AEAFEA');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D13F3F07EC');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D176BDCF5C');
        $this->addSql('ALTER TABLE depot DROP FOREIGN KEY FK_47948BBCF2C56620');
        $this->addSql('ALTER TABLE user_compte_actuel DROP FOREIGN KEY FK_7206AB47F2C56620');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1FB88E14F');
        $this->addSql('ALTER TABLE depot DROP FOREIGN KEY FK_47948BBCB514973B');
        $this->addSql('ALTER TABLE user_compte_actuel DROP FOREIGN KEY FK_7206AB47FB88E14F');
        $this->addSql('DROP TABLE profil');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE depot');
        $this->addSql('DROP TABLE tarifs');
        $this->addSql('DROP TABLE user_compte_actuel');
        $this->addSql('DROP TABLE compte');
        $this->addSql('DROP TABLE utilisateur');
    }
}
