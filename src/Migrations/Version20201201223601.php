<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201201223601 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE station CHANGE city_id city_id INT DEFAULT NULL, CHANGE network_id network_id INT DEFAULT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE from_date from_date DATE DEFAULT NULL COMMENT \'(DC2Type:date)\', CHANGE until_date until_date DATE DEFAULT NULL COMMENT \'(DC2Type:date)\', CHANGE altitude altitude INT DEFAULT NULL, CHANGE station_type station_type ENUM(\'traffic\', \'background\', \'industrial\') DEFAULT NULL COMMENT \'(DC2Type:StationType)\', CHANGE area_type area_type ENUM(\'urban\', \'suburban\', \'rural\') DEFAULT NULL COMMENT \'(DC2Type:AreaType)\', CHANGE provider provider VARCHAR(255) DEFAULT NULL, CHANGE uba_station_id uba_station_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE city CHANGE user_id user_id INT DEFAULT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE fahrverbote_slug fahrverbote_slug VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE network CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE link link VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE twitter_id twitter_id VARCHAR(255) DEFAULT NULL, CHANGE twitter_access_token twitter_access_token VARCHAR(255) DEFAULT NULL, CHANGE twitter_secret twitter_secret VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE twitter_schedule CHANGE station_id station_id INT DEFAULT NULL, CHANGE city_id city_id INT DEFAULT NULL, CHANGE cron cron VARCHAR(255) DEFAULT NULL, CHANGE latitude latitude DOUBLE PRECISION DEFAULT NULL, CHANGE longitude longitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE data MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE data DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE data DROP id, CHANGE station_id station_id INT NOT NULL');
        $this->addSql('ALTER TABLE data ADD PRIMARY KEY (station_id, date_time, pollutant)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE city CHANGE user_id user_id INT DEFAULT NULL, CHANGE description description VARCHAR(255) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`, CHANGE fahrverbote_slug fahrverbote_slug VARCHAR(255) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE data ADD id INT AUTO_INCREMENT NOT NULL, CHANGE station_id station_id INT DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE network CHANGE name name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link link VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE station CHANGE city_id city_id INT DEFAULT NULL, CHANGE network_id network_id INT DEFAULT NULL, CHANGE uba_station_id uba_station_id INT DEFAULT NULL, CHANGE title title VARCHAR(255) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`, CHANGE from_date from_date DATE DEFAULT \'NULL\' COMMENT \'(DC2Type:date)\', CHANGE until_date until_date DATE DEFAULT \'NULL\' COMMENT \'(DC2Type:date)\', CHANGE altitude altitude INT DEFAULT NULL, CHANGE station_type station_type ENUM(\'traffic\', \'background\', \'industrial\') CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:StationType)\', CHANGE area_type area_type ENUM(\'urban\', \'suburban\', \'rural\') CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:AreaType)\', CHANGE provider provider VARCHAR(255) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE twitter_schedule CHANGE station_id station_id INT DEFAULT NULL, CHANGE city_id city_id INT DEFAULT NULL, CHANGE cron cron VARCHAR(255) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`, CHANGE latitude latitude DOUBLE PRECISION DEFAULT \'NULL\', CHANGE longitude longitude DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE twitter_id twitter_id VARCHAR(255) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`, CHANGE twitter_access_token twitter_access_token VARCHAR(255) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`, CHANGE twitter_secret twitter_secret VARCHAR(255) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`');
    }
}
