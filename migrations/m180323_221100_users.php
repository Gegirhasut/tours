<?php

use yii\db\Migration;

/**
 * Class m180323_221100_users
 */
class m180323_221100_users extends Migration
{
    public function up()
    {
        $this->createTable(
            'users',
            [
                'id' => $this->primaryKey(),
                'username' => $this->string(255)->notNull()->unique(),
                'balance' => $this->money(15, 2)->defaultValue(0),
            ],
            'ENGINE InnoDB'
        );
    }

    public function down()
    {
        $this->dropTable('users');

        return false;
    }
}
