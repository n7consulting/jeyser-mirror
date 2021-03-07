<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210220173154 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Phase ADD procesVerbal_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Phase ADD CONSTRAINT FK_707CF9CF7B16E8C5 FOREIGN KEY (procesVerbal_id) REFERENCES ProcesVerbal (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_707CF9CF7B16E8C5 ON Phase (procesVerbal_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Phase DROP FOREIGN KEY FK_707CF9CF7B16E8C5');
        $this->addSql('DROP INDEX IDX_707CF9CF7B16E8C5 ON Phase');
        $this->addSql('ALTER TABLE Phase DROP procesVerbal_id');
    }
}
