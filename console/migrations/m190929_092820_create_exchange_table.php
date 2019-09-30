<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exchange}}`.
 */
class m190929_092820_create_exchange_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%exchange}}', [
            'id' => $this->primaryKey(),
            'api' => $this->string(255),
            'RUBUSD' => $this->float(),
            'USDEUR' => $this->float(),
            'amount_currency' => $this->string(255)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%exchange}}');
    }
}
