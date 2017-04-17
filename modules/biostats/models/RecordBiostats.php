<?php

namespace app\modules\biostats\models;

class RecordBiostats extends Biostats
{
    /**
     * @var float
     */
    protected $sdann;

    /**
     * @var float
     */
    protected $sdnnIndex;

    /**
     * @var float
     */
    protected $rmssd;

    /**
     * @return float
     */
    public function getSdann(): float
    {
        return $this->sdann;
    }

    /**
     * @param float $sdann
     */
    public function setSdann(float $sdann)
    {
        $this->sdann = $sdann;
    }

    /**
     * @return float
     */
    public function getSdnnIndex(): float
    {
        return $this->sdnnIndex;
    }

    /**
     * @param float $sdnnIndex
     */
    public function setSdnnIndex(float $sdnnIndex)
    {
        $this->sdnnIndex = $sdnnIndex;
    }

    /**
     * @return float
     */
    public function getRmssd(): float
    {
        return $this->rmssd;
    }

    /**
     * @param float $rmssd
     */
    public function setRmssd(float $rmssd)
    {
        $this->rmssd = $rmssd;
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
            'sdann' => $this->sdann,
            'sdnn_index' => $this->sdnnIndex,
            'rmssd' => $this->rmssd,
        ];
    }
}
