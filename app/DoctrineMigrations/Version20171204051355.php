<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171204051355 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE twitter_schedule ADD city_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE twitter_schedule ADD CONSTRAINT FK_44F592678BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_44F592678BAC62AF ON twitter_schedule (city_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE twitter_schedule DROP FOREIGN KEY FK_44F592678BAC62AF');
        $this->addSql('DROP INDEX IDX_44F592678BAC62AF ON twitter_schedule');
        $this->addSql('ALTER TABLE twitter_schedule DROP city_id');
    }
}
