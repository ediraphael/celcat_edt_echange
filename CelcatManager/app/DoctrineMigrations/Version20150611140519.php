<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150611140519 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE schedule_modification (id INT AUTO_INCREMENT NOT NULL, id_first_event INT DEFAULT NULL, id_second_event INT DEFAULT NULL, event_id VARCHAR(20) NOT NULL, canceled TINYINT(1) NOT NULL, validated TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_E34ABC002FFC15A (id_first_event), UNIQUE INDEX UNIQ_E34ABC002985B682 (id_second_event), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE schedule_modification ADD CONSTRAINT FK_E34ABC002FFC15A FOREIGN KEY (id_first_event) REFERENCES event_modification (id)');
        $this->addSql('ALTER TABLE schedule_modification ADD CONSTRAINT FK_E34ABC002985B682 FOREIGN KEY (id_second_event) REFERENCES event_modification (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE schedule_modification');
    }
}
