<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210428095228 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_article (category_id INT NOT NULL, article_id INT NOT NULL, INDEX IDX_C5E24E1812469DE2 (category_id), INDEX IDX_C5E24E187294869C (article_id), PRIMARY KEY(category_id, article_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_article (tag_id INT NOT NULL, article_id INT NOT NULL, INDEX IDX_300B23CCBAD26311 (tag_id), INDEX IDX_300B23CC7294869C (article_id), PRIMARY KEY(tag_id, article_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, city_agency VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE version (id INT AUTO_INCREMENT NOT NULL, contributor_id INT DEFAULT NULL, validator_id INT DEFAULT NULL, article_id INT NOT NULL, comment LONGTEXT DEFAULT NULL, content LONGTEXT DEFAULT NULL, modification_date DATETIME DEFAULT NULL, is_validated TINYINT(1) NOT NULL, INDEX IDX_BF1CD3C37A19A357 (contributor_id), INDEX IDX_BF1CD3C3B0644AEC (validator_id), INDEX IDX_BF1CD3C37294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_article ADD CONSTRAINT FK_C5E24E1812469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_article ADD CONSTRAINT FK_C5E24E187294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_article ADD CONSTRAINT FK_300B23CCBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_article ADD CONSTRAINT FK_300B23CC7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE version ADD CONSTRAINT FK_BF1CD3C37A19A357 FOREIGN KEY (contributor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE version ADD CONSTRAINT FK_BF1CD3C3B0644AEC FOREIGN KEY (validator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE version ADD CONSTRAINT FK_BF1CD3C37294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE article ADD creator_id INT NOT NULL, ADD current_version_id INT DEFAULT NULL, ADD is_published TINYINT(1) NOT NULL, ADD is_deleted TINYINT(1) NOT NULL, ADD publication_date DATETIME DEFAULT NULL, ADD creation_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6661220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E669407EE77 FOREIGN KEY (current_version_id) REFERENCES version (id)');
        $this->addSql('CREATE INDEX IDX_23A0E6661220EA6 ON article (creator_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23A0E669407EE77 ON article (current_version_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_article DROP FOREIGN KEY FK_C5E24E1812469DE2');
        $this->addSql('ALTER TABLE tag_article DROP FOREIGN KEY FK_300B23CCBAD26311');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E6661220EA6');
        $this->addSql('ALTER TABLE version DROP FOREIGN KEY FK_BF1CD3C37A19A357');
        $this->addSql('ALTER TABLE version DROP FOREIGN KEY FK_BF1CD3C3B0644AEC');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E669407EE77');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_article');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_article');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE version');
        $this->addSql('DROP INDEX IDX_23A0E6661220EA6 ON article');
        $this->addSql('DROP INDEX UNIQ_23A0E669407EE77 ON article');
        $this->addSql('ALTER TABLE article DROP creator_id, DROP current_version_id, DROP is_published, DROP is_deleted, DROP publication_date, DROP creation_date');
    }
}
