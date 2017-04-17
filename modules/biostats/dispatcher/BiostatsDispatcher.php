<?php

namespace app\modules\biostats\dispatcher;

use app\modules\biostats\dispatcher\handlers\RecordBiostatsHandler;
use app\modules\biostats\dispatcher\handlers\UserRequestBiostatsHandler;
use app\modules\biostats\dispatcher\request\BiostatsRequest;
use app\modules\biostats\dispatcher\request\RecordBiostatsRequest;
use app\modules\biostats\dispatcher\request\UserRequestBiostatsRequest;
use app\modules\biostats\models\Biostats;

class BiostatsDispatcher
{
    private $handlers = [
        RecordBiostatsRequest::class => RecordBiostatsHandler::class,
        UserRequestBiostatsRequest::class => UserRequestBiostatsHandler::class,
    ];

    /**
     * @param BiostatsRequest $request
     * @return Biostats
     * @throws \Exception
     */
    public function dispatch(BiostatsRequest $request)
    {
        foreach ($this->handlers as $requestType => $handler) {
            if ($request instanceof $requestType) {
                /** @var BiostatsHandlerInterface $biostatsHandler */
                $biostatsHandler = new $handler();

                return $biostatsHandler->handle($request);
            }
        }

        throw new \Exception('None handler can handle this biostats request');
    }
}
