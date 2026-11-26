<?php
namespace verbb\patrol\migrations;

use verbb\patrol\db\Table;

use craft\db\Migration;

class Install extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        $this->createTables();
        $this->createIndexes();

        return true;
    }

    public function safeDown(): bool
    {
        $this->dropTableIfExists(Table::AUTHORIZED_IPS);

        return true;
    }

    public function createTables(): void
    {
        $this->archiveTableIfExists(Table::AUTHORIZED_IPS);
        $this->createTable(Table::AUTHORIZED_IPS, [
            'id' => $this->primaryKey(),
            'ip' => $this->string(45)->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);
    }

    public function createIndexes(): void
    {
        $this->createIndex(null, Table::AUTHORIZED_IPS, ['ip'], true);
    }
}
