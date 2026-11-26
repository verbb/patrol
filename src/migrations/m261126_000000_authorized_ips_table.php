<?php
namespace verbb\patrol\migrations;

use verbb\patrol\db\Table;

use craft\db\Migration;

class m261126_000000_authorized_ips_table extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        if (!$this->db->tableExists(Table::AUTHORIZED_IPS)) {
            $this->createTable(Table::AUTHORIZED_IPS, [
                'id' => $this->primaryKey(),
                'ip' => $this->string(45)->notNull(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);

            $this->createIndex(null, Table::AUTHORIZED_IPS, ['ip'], true);
        }

        return true;
    }

    public function safeDown(): bool
    {
        echo "m261126_000000_authorized_ips_table cannot be reverted.\n";

        return false;
    }
}
