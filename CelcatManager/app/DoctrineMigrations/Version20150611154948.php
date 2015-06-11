<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150611154948 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event_modification CHANGE start_datetime_initial start_datetime_initial DATETIME NOT NULL, CHANGE end_datetime_initial end_datetime_initial DATETIME NOT NULL, CHANGE start_datetime_final start_datetime_final DATETIME NOT NULL, CHANGE end_datetime_final end_datetime_final DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event_modification CHANGE start_datetime_initial start_datetime_initial DATE NOT NULL, CHANGE end_datetime_initial end_datetime_initial DATE NOT NULL, CHANGE start_datetime_final start_datetime_final DATE NOT NULL, CHANGE end_datetime_final end_datetime_final DATE NOT NULL');
    }
}
