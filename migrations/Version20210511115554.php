<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210511115554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E6661220EA6');
        $this->addSql('ALTER TABLE article CHANGE creator_id creator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6661220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD validated TINYINT(1) NOT NULL, CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE version DROP FOREIGN KEY FK_BF1CD3C37294869C');
        $this->addSql('ALTER TABLE version DROP FOREIGN KEY FK_BF1CD3C37A19A357');
        $this->addSql('ALTER TABLE version ADD CONSTRAINT FK_BF1CD3C37294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE version ADD CONSTRAINT FK_BF1CD3C37A19A357 FOREIGN KEY (contributor_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E6661220EA6');
        $this->addSql('ALTER TABLE article CHANGE creator_id creator_id INT NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6661220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user DROP validated, CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE version DROP FOREIGN KEY FK_BF1CD3C37A19A357');
        $this->addSql('ALTER TABLE version DROP FOREIGN KEY FK_BF1CD3C37294869C');
        $this->addSql('ALTER TABLE version ADD CONSTRAINT FK_BF1CD3C37A19A357 FOREIGN KEY (contributor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE version ADD CONSTRAINT FK_BF1CD3C37294869C FOREIGN KEY (article_id) REFERENCES article (id)');
    }
}
