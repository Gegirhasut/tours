<?php

use yii\db\Migration;

/**
 * Class m180324_142022_history
 */
class m180324_142022_history extends Migration
{
    public function up()
    {
        $this->createTable(
            'history',
            [
                'id' => $this->primaryKey(),
                'user' => $this->string(255)->notNull(),
                'amount' => $this->money(15, 2),
                'balance' =>  $this->money(15, 2),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')
            ],
            'ENGINE InnoDB'
        );
    }

    public function down()
    {
        $this->dropTable('history');

        return false;
    }
}
