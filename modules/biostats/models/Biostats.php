<?php

namespace app\modules\biostats\models;

abstract class Biostats
{
    /**
     * @var float
     */
    protected $meanRR;

    /**
     * @var float
     */
    protected $heartRate;

    /**
     * @var float
     */
    protected $range;

    /**
     * @var float
     */
    protected $nn50;

    /**
     * @var float
     */
    protected $pnn50;

    /**
     * @return float
     */
    public function getMeanRR(): float
    {
        return $this->meanRR;
    }

    /**
     * @param float $meanRR
     */
    public function setMeanRR(float $meanRR)
    {
        $this->meanRR = $meanRR;
    }

    /**
     * @return float
     */
    public function getHeartRate(): float
    {
        return $this->heartRate;
    }

    /**
     * @param float $heartRate
     */
    public function setHeartRate(float $heartRate)
    {
        $this->heartRate = $heartRate;
    }

    /**
     * @return float
     */
    public function getRange(): float
    {
        return $this->range;
    }

    /**
     * @param float $range
     */
    public function setRange(float $range)
    {
        $this->range = $range;
    }

    /**
     * @return float
     */
    public function getNn50(): float
    {
        return $this->nn50;
    }

    /**
     * @param float $nn50
     */
    public function setNn50(float $nn50)
    {
        $this->nn50 = $nn50;
    }

    /**
     * @return float
     */
    public function getPnn50(): float
    {
        return $this->pnn50;
    }

    /**
     * @param float $pnn50
     */
    public function setPnn50(float $pnn50)
    {
        $this->pnn50 = $pnn50;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'mean_r_r' => $this->meanRR,
            'heart_rate' => $this->heartRate,
            'range' => $this->range,
            'nn50' => $this->nn50,
            'pnn50' => $this->pnn50,
        ];
    }
}
