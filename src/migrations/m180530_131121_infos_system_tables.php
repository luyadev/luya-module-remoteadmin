<?php

use yii\db\Migration;

/**
 * Class m180530_131121_infos_system_tables
 */
class m180530_131121_infos_system_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {   
        $this->createTable('remote_message_log', [
            'id' => $this->primaryKey(),
            'site_id' => $this->integer(),
            'timestamp' => $this->integer()->notNull(),
            'recipients' => $this->string()->notNull(),
            'text' => $this->text()->notNull(),
        ]);
        
        $this->createTable('remote_message_template', [
            'id' => $this->primaryKey(),
            'title' => $this->text()->notNull(),
            'subject' => $this->text()->notNull(),
            'text' => $this->text()->notNull(),
            'is_default' => $this->boolean()->defaultValue(false),
        ]);
        
        $this->createTable('remote_billing_product', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(), // hosting small
            'month_cycle' => $this->integer(), // 1 = every 1 month
            'price' => $this->integer(), // in the lowest unit of the currency: 100 (cents)
        ]);
        
        // not using composite primary key as a product can be used multiple times for a given site.
        $this->createTable('remote_site_billing_product', [
            'id' => $this->primaryKey(),
            'billing_product_id' => $this->integer(),
            'site_id' => $this->integer(),
        ]);
        
        $this->addColumn('remote_site', 'recipient', $this->string());
        $this->addColumn('remote_site', 'last_message_timestamp', $this->integer());
        $this->addColumn('remote_site', 'is_deleted', $this->boolean()->defaultValue(false));
        $this->addColumn('remote_site', 'billing_start_timestamp', $this->integer());
        $this->addColumn('remote_site', 'status', $this->integer());
        $this->addColumn('remote_site', 'auto_update_message', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('remote_message_log');
        $this->dropTable('remote_message_template');
        $this->dropTable('remote_billing_product');
        $this->dropTable('remote_site_billing_product');
        
        $this->dropColumn('remote_site', 'recipient');
        $this->dropColumn('remote_site', 'last_message_timestamp');
        $this->dropColumn('remote_site', 'is_deleted');
        $this->dropColumn('remote_site', 'billing_start_timestamp');
        $this->dropColumn('remote_site', 'status');
        $this->dropColumn('remote_site', 'auto_update_message');
    }
}
