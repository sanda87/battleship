<?php

use yii\db\Migration;

/**
 * Handles the creation for table `play_field`.
 */
class m161011_061244_create_play_field_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('play_field', [
            'id' => $this->primaryKey(),
            'filled_points' => $this->text()->notNull(),
            'created_at' => $this->integer()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('play_field');
    }
}
