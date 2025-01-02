<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241230152132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE coordinates (id INT AUTO_INCREMENT NOT NULL, route_id INT NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, elevation DOUBLE PRECISION NOT NULL, INDEX IDX_9816D67634ECB4E6 (route_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE route (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE coordinates ADD CONSTRAINT FK_9816D67634ECB4E6 FOREIGN KEY (route_id) REFERENCES route (id)');
        $this->addSql('ALTER TABLE article CHANGE category_id category_id INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23A0E66989D9B62 ON article (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_53A4EDAA989D9B62 ON article_category (slug)');
        $this->addSql('ALTER TABLE comment ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE article_id article_id INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9D40DE1B989D9B62 ON topic (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F07D94C7989D9B62 ON topic_category (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coordinates DROP FOREIGN KEY FK_9816D67634ECB4E6');
        $this->addSql('DROP TABLE coordinates');
        $this->addSql('DROP TABLE route');
        $this->addSql('DROP INDEX UNIQ_F07D94C7989D9B62 ON topic_category');
        $this->addSql('DROP INDEX UNIQ_9D40DE1B989D9B62 ON topic');
        $this->addSql('DROP INDEX UNIQ_23A0E66989D9B62 ON article');
        $this->addSql('ALTER TABLE article CHANGE category_id category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment DROP created_at, CHANGE article_id article_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_53A4EDAA989D9B62 ON article_category');
    }
}
