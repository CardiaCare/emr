<?php

namespace app\modules\biostats\dispatcher\handlers;

use app\modules\biostats\models\Biostats;
use app\modules\biostats\models\UserRequestBiostats;
use app\modules\biostats\dispatcher\BiostatsHandlerInterface;
use app\modules\biostats\dispatcher\request\BiostatsRequest;
use app\modules\biostats\dispatcher\request\UserRequestBiostatsRequest;

class UserRequestBiostatsHandler implements BiostatsHandlerInterface
{
    /**
     * @param BiostatsRequest $request
     * @return Biostats
     */
    public function handle(BiostatsRequest $request): Biostats
    {
        if (!($request instanceof UserRequestBiostatsRequest)) {
            throw new \InvalidArgumentException();
        }

        return $this->createBiostats();
    }

    /**
     * @return UserRequestBiostats
     */
    private function createBiostats()
    {
        $biostats = new UserRequestBiostats();
        $biostats->setHeartRate(80.0);
        $biostats->setMeanRR(20.0);
        $biostats->setNn50(50.0);
        $biostats->setPnn50(51.0);
        $biostats->setRange(100.0);
        $biostats->setAmo(100.0);
        $biostats->setIvb(30.0);
        $biostats->setRpai(20.0);
        $biostats->setTensionIndex(549.0);
        $biostats->setTI(55.0);
        $biostats->setVri(123.0);

        return $biostats;
    }
}