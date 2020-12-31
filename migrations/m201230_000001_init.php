<?php

use yii\db\Schema;
use yii\db\Migration;

class m201230_000001_init extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Create table user which store user login credentials, verification status and personal data
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'lang' => $this->string(16),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->insert('{{%user}}', [
            'id' => 1,
            'username' => 'administrator',
            // password: "administrator"
            'password_hash' => '$2y$13$e1dEJFbLQiaFOrJtucp5wOLuFP3O1217OOwt4UxDxK2xuRKnPjvKK',
            'email' => 'test@test.test',
            'status' => 10,
            'auth_key' => 'auth_key_administrator',
            'created_at' => 0,
            'updated_at' => 0,
        ]);
    }

    public function down()
    {
        echo "m151120_000001_init is irreversible\n";
        return false;
    }
}
