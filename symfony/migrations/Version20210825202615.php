<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210825202615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update the resource and resource_type for different URL configurations';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE resource ADD request_url VARCHAR(255) NOT NULL AFTER `json`');
        $this->addSql('ALTER TABLE resource_type ADD url_type INT NOT NULL AFTER `slug`');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE resource DROP request_url');
        $this->addSql('ALTER TABLE resource_type DROP url_type');
    }
}
