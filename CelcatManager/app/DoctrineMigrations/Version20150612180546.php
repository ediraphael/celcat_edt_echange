<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150612180546 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
	$this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
	$this->addSql('INSERT INTO model_mail ( `from_address`, `to_address`, `subject`, `body`, `name`) VALUES
(\'test@example.fr\', \'test@example.fr\', \'Modification EDT\', \'<p>Bonjour,</p>\n\n<p>je souhaiterais effectuer les modifications suivante sur mon EDT:</p>\n\n<p>[schedule_modification]</p>\n\n<p>Cordialement</p>\n\n<p>[user_fullname]</p>\', \'schedule_modification\'),
(\'test@example.fr\', \'test@example.fr\', \'Demande d echange\', \'<p>Bonjour,</p>\n\n<p>je souhaiterai échanger nos créneaux horaires.</p>\n\n<p>[schedule_modification]</p>\n\n<p>Cordialement</p>\n\n<p>[user_fullname]</p>\', \'schedule_user_ask\'),
(\'test@example.fr\', \'test@example.fr\', \'Validation d echange\', \'<p>Bonjour,</p>\r\n\r\n<p>J ai validé votre demande de modification.</p>\r\n\r\n<p>[schedule_modification]</p>\r\n\r\n<p>Cordialement</p>\r\n\r\n<p>[user_fullname]</p>\', \'schedule_user_validation\')');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
