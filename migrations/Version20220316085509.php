<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220316085509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, vicinity VARCHAR(255) DEFAULT NULL, street VARCHAR(255) NOT NULL, number VARCHAR(255) DEFAULT NULL, city VARCHAR(255) NOT NULL, region VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(50) NOT NULL, country VARCHAR(50) NOT NULL, longitude VARCHAR(50) NOT NULL, latitude VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, reference VARCHAR(50) NOT NULL, label VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gas_price (id INT AUTO_INCREMENT NOT NULL, gas_station_id VARCHAR(255) NOT NULL, gas_type_id INT NOT NULL, currency_id INT NOT NULL, value INT NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_timestamp INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_EEF8FDB6916BFF50 (gas_station_id), INDEX IDX_EEF8FDB63145108E (gas_type_id), INDEX IDX_EEF8FDB638248176 (currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gas_service (id INT AUTO_INCREMENT NOT NULL, reference VARCHAR(150) NOT NULL, label VARCHAR(150) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gas_stations_services (gas_service_id INT NOT NULL, gas_station_id VARCHAR(255) NOT NULL, INDEX IDX_FB9897DF5D8AE483 (gas_service_id), INDEX IDX_FB9897DF916BFF50 (gas_station_id), PRIMARY KEY(gas_service_id, gas_station_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gas_station (id VARCHAR(255) NOT NULL, gas_station_status_id INT NOT NULL, address_id INT NOT NULL, preview_id INT NOT NULL, google_place_id INT NOT NULL, pop VARCHAR(10) NOT NULL, name VARCHAR(255) DEFAULT NULL, company VARCHAR(255) DEFAULT NULL, closed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', element LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', last_gas_prices LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6B3064AC98FCD035 (gas_station_status_id), UNIQUE INDEX UNIQ_6B3064ACF5B7AF75 (address_id), INDEX IDX_6B3064ACCDE46FDB (preview_id), INDEX IDX_6B3064AC983C031 (google_place_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gas_station_status (id INT AUTO_INCREMENT NOT NULL, reference VARCHAR(50) NOT NULL, label VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gas_station_status_history (id INT AUTO_INCREMENT NOT NULL, gas_station_id VARCHAR(255) NOT NULL, gas_station_status_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_BB6C189B916BFF50 (gas_station_id), INDEX IDX_BB6C189B98FCD035 (gas_station_status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gas_type (id INT NOT NULL, reference VARCHAR(50) NOT NULL, label VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE google_place (id INT AUTO_INCREMENT NOT NULL, google_id VARCHAR(15) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(20) DEFAULT NULL, place_id VARCHAR(50) DEFAULT NULL, compound_code VARCHAR(50) DEFAULT NULL, global_code VARCHAR(50) DEFAULT NULL, google_rating VARCHAR(10) DEFAULT NULL, rating VARCHAR(10) DEFAULT NULL, reference VARCHAR(50) DEFAULT NULL, user_ratings_total VARCHAR(10) DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, business_status VARCHAR(50) DEFAULT NULL, opening_hours LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(50) DEFAULT NULL, name VARCHAR(100) NOT NULL, mime_type VARCHAR(25) DEFAULT NULL, type VARCHAR(25) DEFAULT NULL, size DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(200) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gas_price ADD CONSTRAINT FK_EEF8FDB6916BFF50 FOREIGN KEY (gas_station_id) REFERENCES gas_station (id)');
        $this->addSql('ALTER TABLE gas_price ADD CONSTRAINT FK_EEF8FDB63145108E FOREIGN KEY (gas_type_id) REFERENCES gas_type (id)');
        $this->addSql('ALTER TABLE gas_price ADD CONSTRAINT FK_EEF8FDB638248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('ALTER TABLE gas_stations_services ADD CONSTRAINT FK_FB9897DF5D8AE483 FOREIGN KEY (gas_service_id) REFERENCES gas_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gas_stations_services ADD CONSTRAINT FK_FB9897DF916BFF50 FOREIGN KEY (gas_station_id) REFERENCES gas_station (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gas_station ADD CONSTRAINT FK_6B3064AC98FCD035 FOREIGN KEY (gas_station_status_id) REFERENCES gas_station_status (id)');
        $this->addSql('ALTER TABLE gas_station ADD CONSTRAINT FK_6B3064ACF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE gas_station ADD CONSTRAINT FK_6B3064ACCDE46FDB FOREIGN KEY (preview_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE gas_station ADD CONSTRAINT FK_6B3064AC983C031 FOREIGN KEY (google_place_id) REFERENCES google_place (id)');
        $this->addSql('ALTER TABLE gas_station_status_history ADD CONSTRAINT FK_BB6C189B916BFF50 FOREIGN KEY (gas_station_id) REFERENCES gas_station (id)');
        $this->addSql('ALTER TABLE gas_station_status_history ADD CONSTRAINT FK_BB6C189B98FCD035 FOREIGN KEY (gas_station_status_id) REFERENCES gas_station_status (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gas_station DROP FOREIGN KEY FK_6B3064ACF5B7AF75');
        $this->addSql('ALTER TABLE gas_price DROP FOREIGN KEY FK_EEF8FDB638248176');
        $this->addSql('ALTER TABLE gas_stations_services DROP FOREIGN KEY FK_FB9897DF5D8AE483');
        $this->addSql('ALTER TABLE gas_price DROP FOREIGN KEY FK_EEF8FDB6916BFF50');
        $this->addSql('ALTER TABLE gas_stations_services DROP FOREIGN KEY FK_FB9897DF916BFF50');
        $this->addSql('ALTER TABLE gas_station_status_history DROP FOREIGN KEY FK_BB6C189B916BFF50');
        $this->addSql('ALTER TABLE gas_station DROP FOREIGN KEY FK_6B3064AC98FCD035');
        $this->addSql('ALTER TABLE gas_station_status_history DROP FOREIGN KEY FK_BB6C189B98FCD035');
        $this->addSql('ALTER TABLE gas_price DROP FOREIGN KEY FK_EEF8FDB63145108E');
        $this->addSql('ALTER TABLE gas_station DROP FOREIGN KEY FK_6B3064AC983C031');
        $this->addSql('ALTER TABLE gas_station DROP FOREIGN KEY FK_6B3064ACCDE46FDB');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE gas_price');
        $this->addSql('DROP TABLE gas_service');
        $this->addSql('DROP TABLE gas_stations_services');
        $this->addSql('DROP TABLE gas_station');
        $this->addSql('DROP TABLE gas_station_status');
        $this->addSql('DROP TABLE gas_station_status_history');
        $this->addSql('DROP TABLE gas_type');
        $this->addSql('DROP TABLE google_place');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
