<?php

namespace app\modules\biostats\dispatcher\handlers;

use app\modules\biostats\models\Biostats;
use app\modules\biostats\dispatcher\BiostatsHandlerInterface;
use app\modules\biostats\models\RecordBiostats;
use app\modules\biostats\dispatcher\request\BiostatsRequest;
use app\modules\biostats\dispatcher\request\RecordBiostatsRequest;

class RecordBiostatsHandler implements BiostatsHandlerInterface
{
    /**
     * @param BiostatsRequest $request
     * @return Biostats
     */
    public function handle(BiostatsRequest $request): Biostats
    {
        if (!($request instanceof RecordBiostatsRequest)) {
            throw new \InvalidArgumentException();
        }

        return $this->createBiostats();
    }

    /**
     * @return RecordBiostats
     */
    private function createBiostats()
    {
        $biostats = new RecordBiostats();
        $biostats->setHeartRate(80.0);
        $biostats->setMeanRR(20.0);
        $biostats->setNn50(50.0);
        $biostats->setPnn50(51.0);
        $biostats->setRange(100.0);
        $biostats->setRmssd(30.0);
        $biostats->setSdann(123.0);
        $biostats->setSdnnIndex(5.0);

        return $biostats;
    }
}
