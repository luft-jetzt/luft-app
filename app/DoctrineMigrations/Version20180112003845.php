<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180112003845 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_city');
        $this->addSql('ALTER TABLE city DROP twitter_token, DROP twitter_secret, DROP twitter_username');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_city (city_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_57DA4EFD8BAC62AF (city_id), INDEX IDX_57DA4EFDA76ED395 (user_id), PRIMARY KEY(city_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_city ADD CONSTRAINT FK_57DA4EFD8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE user_city ADD CONSTRAINT FK_57DA4EFDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE city ADD twitter_token VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD twitter_secret VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD twitter_username VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
