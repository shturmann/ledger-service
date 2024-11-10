<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241110162801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create initial ledger service tables';
    }

    public function up(Schema $schema): void
    {
        // Create ledger table
        $this->addSql('CREATE TABLE ledger (
            id SERIAL PRIMARY KEY,
            identifier VARCHAR(36) NOT NULL UNIQUE,
            default_currency VARCHAR(3) NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
        )');

        // Create transaction table
        $this->addSql('CREATE TABLE transaction (
            id SERIAL PRIMARY KEY,
            ledger_id INT NOT NULL,
            transaction_id VARCHAR(36) NOT NULL UNIQUE,
            type VARCHAR(10) NOT NULL,
            amount NUMERIC(20, 4) NOT NULL,
            currency VARCHAR(3) NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_transaction_ledger FOREIGN KEY (ledger_id) REFERENCES ledger (id) ON DELETE RESTRICT
        )');

        // Create balance table
        $this->addSql('CREATE TABLE balance (
            id SERIAL PRIMARY KEY,
            ledger_id INT NOT NULL,
            currency VARCHAR(3) NOT NULL,
            amount NUMERIC(20, 4) NOT NULL DEFAULT 0,
            version INT NOT NULL DEFAULT 1,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_balance_ledger FOREIGN KEY (ledger_id) REFERENCES ledger (id) ON DELETE RESTRICT,
            CONSTRAINT uq_ledger_currency UNIQUE (ledger_id, currency)
        )');

        // Create indexes
        $this->addSql('CREATE INDEX idx_transaction_ledger ON transaction (ledger_id)');
        $this->addSql('CREATE INDEX idx_transaction_created ON transaction (created_at)');
        $this->addSql('CREATE INDEX idx_balance_ledger ON balance (ledger_id)');
        $this->addSql('CREATE INDEX idx_ledger_identifier ON ledger (identifier)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS transaction');
        $this->addSql('DROP TABLE IF EXISTS balance');
        $this->addSql('DROP TABLE IF EXISTS ledger');
    }
}
