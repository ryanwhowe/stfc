<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210827163854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add the ResourceDetail table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE resource_detail (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, scopley_id BIGINT NOT NULL, request_url VARCHAR(255) NOT NULL, json JSON NOT NULL, last_updated DATETIME NOT NULL, created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_E6FA9569714819A0 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE resource_detail ADD CONSTRAINT FK_E6FA9569714819A0 FOREIGN KEY (type_id) REFERENCES resource_type (id)');
        $this->addSql('CREATE INDEX IDX_E6FA9569DF6A4D98 ON resource_detail (scopley_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE resource_detail');
    }
}
