<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231111210054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE switches (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, state TINYINT(1) NOT NULL, click_date_start DATETIME DEFAULT NULL, click_date_end DATETIME DEFAULT NULL, public_uri BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', private_uri BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_F34AC2337E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(180) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_switch (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, switch_id INT DEFAULT NULL, INDEX IDX_ABB5025A76ED395 (user_id), INDEX IDX_ABB5025BE2FFB85 (switch_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE switches ADD CONSTRAINT FK_F34AC2337E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_switch ADD CONSTRAINT FK_ABB5025A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_switch ADD CONSTRAINT FK_ABB5025BE2FFB85 FOREIGN KEY (switch_id) REFERENCES switches (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE switches DROP FOREIGN KEY FK_F34AC2337E3C61F9');
        $this->addSql('ALTER TABLE user_switch DROP FOREIGN KEY FK_ABB5025A76ED395');
        $this->addSql('ALTER TABLE user_switch DROP FOREIGN KEY FK_ABB5025BE2FFB85');
        $this->addSql('DROP TABLE switches');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_switch');
    }
}
