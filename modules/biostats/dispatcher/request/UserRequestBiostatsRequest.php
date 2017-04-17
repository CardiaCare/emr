<?php

namespace app\modules\biostats\dispatcher\request;

use app\modules\emr\models\Patient;

class UserRequestBiostatsRequest extends BiostatsRequest
{
    /**
     * @var \DateTime
     */
    protected $startDate;

    /**
     * @var \DateTime
     */
    protected $endDate;

    /**
     * @var Patient
     */
    protected $patient;

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param Patient $patient
     */
    public function __construct(\DateTime $startDate = null, \DateTime $endDate = null, Patient $patient = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->patient = $patient;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return Patient
     */
    public function getPatient(): Patient
    {
        return $this->patient;
    }

    /**
     * @param Patient $patient
     */
    public function setPatient(Patient $patient)
    {
        $this->patient = $patient;
    }
}
