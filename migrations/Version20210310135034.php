<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210310135034 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE juego (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, fecha_lanzamineto DATE DEFAULT NULL, genero VARCHAR(255) DEFAULT NULL, plataforma VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lista (id INT AUTO_INCREMENT NOT NULL, usuario_id INT NOT NULL, titulo VARCHAR(255) NOT NULL, INDEX IDX_FB9FEEEDDB38439E (usuario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lista_juego (lista_id INT NOT NULL, juego_id INT NOT NULL, INDEX IDX_1B58B0806736D68F (lista_id), INDEX IDX_1B58B08013375255 (juego_id), PRIMARY KEY(lista_id, juego_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lista ADD CONSTRAINT FK_FB9FEEEDDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE lista_juego ADD CONSTRAINT FK_1B58B0806736D68F FOREIGN KEY (lista_id) REFERENCES lista (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lista_juego ADD CONSTRAINT FK_1B58B08013375255 FOREIGN KEY (juego_id) REFERENCES juego (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lista_juego DROP FOREIGN KEY FK_1B58B08013375255');
        $this->addSql('ALTER TABLE lista_juego DROP FOREIGN KEY FK_1B58B0806736D68F');
        $this->addSql('DROP TABLE juego');
        $this->addSql('DROP TABLE lista');
        $this->addSql('DROP TABLE lista_juego');
    }
}
