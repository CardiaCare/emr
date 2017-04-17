<?php

use yii\db\Migration;

class m170417_034525_add_height_and_weight_to_patient extends Migration
{
    public function up()
    {
        $this->addColumn('patient', 'height', $this->float());
        $this->addColumn('patient', 'weight', $this->float());
    }

    public function down()
    {
        $this->dropColumn('patient', 'height');
        $this->dropColumn('patient', 'weight');
    }
}
