<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231124200121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE basket_item (id INT AUTO_INCREMENT NOT NULL, book_info_id INT DEFAULT NULL, count INT NOT NULL, UNIQUE INDEX UNIQ_D4943C2BCEABE96E (book_info_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE basket_item ADD CONSTRAINT FK_D4943C2BCEABE96E FOREIGN KEY (book_info_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3311BE1FB52');
        $this->addSql('DROP INDEX IDX_CBE5A3311BE1FB52 ON book');
        $this->addSql('ALTER TABLE book DROP basket_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket_item DROP FOREIGN KEY FK_D4943C2BCEABE96E');
        $this->addSql('DROP TABLE basket_item');
        $this->addSql('ALTER TABLE book ADD basket_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3311BE1FB52 FOREIGN KEY (basket_id) REFERENCES basket (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_CBE5A3311BE1FB52 ON book (basket_id)');
    }
}
