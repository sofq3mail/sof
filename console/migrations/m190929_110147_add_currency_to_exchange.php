<?php

use yii\db\Migration;

/**
 * Class m190929_110147_add_currency_to_exchange
 */
class m190929_110147_add_currency_to_exchange extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%exchange}}','currency',$this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190929_110147_add_currency_to_exchange cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190929_110147_add_currency_to_exchange cannot be reverted.\n";

        return false;
    }
    */
}
