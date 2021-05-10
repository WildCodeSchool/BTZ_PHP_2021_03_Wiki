<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210510140047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE version DROP FOREIGN KEY FK_BF1CD3C37294869C');
        $this->addSql('ALTER TABLE version DROP FOREIGN KEY FK_BF1CD3C37A19A357');
        $this->addSql('ALTER TABLE version ADD CONSTRAINT FK_BF1CD3C37294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE version ADD CONSTRAINT FK_BF1CD3C37A19A357 FOREIGN KEY (contributor_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE version DROP FOREIGN KEY FK_BF1CD3C37A19A357');
        $this->addSql('ALTER TABLE version DROP FOREIGN KEY FK_BF1CD3C37294869C');
        $this->addSql('ALTER TABLE version ADD CONSTRAINT FK_BF1CD3C37A19A357 FOREIGN KEY (contributor_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE version ADD CONSTRAINT FK_BF1CD3C37294869C FOREIGN KEY (article_id) REFERENCES article (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
