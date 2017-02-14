<?php

use yii\db\Migration;

class m170214_113345_bloodpressure_fix_created_at extends Migration
{
    public function up()
    {
        $this->dropColumn('bloodpressure', 'created_at');
        $this->addColumn('bloodpressure', 'created_at', $this->dateTime()->notNull());
    }

    public function down()
    {
        $this->dropColumn('bloodpressure', 'created_at');
        $this->addColumn('bloodpressure', 'created_at', $this->integer());
    }
}
