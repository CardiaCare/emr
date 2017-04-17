<?php

namespace app\modules\biostats\dispatcher;

use app\modules\biostats\models\Biostats;
use app\modules\biostats\dispatcher\request\BiostatsRequest;

interface BiostatsHandlerInterface
{
    /**
     * @param BiostatsRequest $request
     * @return Biostats
     */
    public function handle(BiostatsRequest $request): Biostats;
}
