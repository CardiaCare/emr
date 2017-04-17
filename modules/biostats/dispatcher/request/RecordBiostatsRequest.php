<?php

namespace app\modules\biostats\dispatcher\request;

use app\modules\emr\models\Biosignal;
use app\modules\emr\models\BloodPressure;

class RecordBiostatsRequest extends BiostatsRequest
{
    /**
     * @var Biosignal
     */
    protected $biosignal;

    /**
     * @var BloodPressure
     */
    protected $bloodPressure;

    /**
     * @param Biosignal $biosignal
     * @param BloodPressure $bloodPressure
     */
    public function __construct(Biosignal $biosignal = null, BloodPressure $bloodPressure = null)
    {
        $this->biosignal = $biosignal;
        $this->bloodPressure = $bloodPressure;
    }

    /**
     * @return Biosignal
     */
    public function getBiosignal(): Biosignal
    {
        return $this->biosignal;
    }

    /**
     * @param Biosignal $biosignal
     */
    public function setBiosignal(Biosignal $biosignal)
    {
        $this->biosignal = $biosignal;
    }

    /**
     * @return BloodPressure
     */
    public function getBloodPressure(): BloodPressure
    {
        return $this->bloodPressure;
    }

    /**
     * @param BloodPressure $bloodPressure
     */
    public function setBloodPressure(BloodPressure $bloodPressure)
    {
        $this->bloodPressure = $bloodPressure;
    }
}
