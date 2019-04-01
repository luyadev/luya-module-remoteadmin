<?php

use yii\db\Migration;

/**
 * Class m190401_135616_site_groups
 */
class m190401_135616_site_groups extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('remote_site_group', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
        ]);

        $this->addColumn('remote_site', 'group_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('remote_site_group');
        $this->dropColumn('remote_site', 'group_id');
    }
}
