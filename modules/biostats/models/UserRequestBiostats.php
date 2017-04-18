<?php

namespace app\modules\biostats\models;

class UserRequestBiostats extends Biostats
{
    /**
     * @var float
     */
    protected $amo;


    /**
     * @var float
     */
    protected $ti;

    /**
     * @var float
     */
    protected $ivb;

    /**
     * @var float
     */
    protected $rpai;

    /**
     * @var float
     */
    protected $vri;

    /**
     * @var float
     */
    protected $tensionIndex;

    /**
     * @return float
     */
    public function getAmo(): float
    {
        return $this->amo;
    }

    /**
     * @param float $amo
     */
    public function setAmo(float $amo)
    {
        $this->amo = $amo;
    }

    /**
     * @return float
     */
    public function getTI(): float
    {
        return $this->ti;
    }

    /**
     * @param float $ti
     */
    public function setTI(float $ti)
    {
        $this->ti = $ti;
    }

    /**
     * @return float
     */
    public function getIvb(): float
    {
        return $this->ivb;
    }

    /**
     * @param float $ivb
     */
    public function setIvb(float $ivb)
    {
        $this->ivb = $ivb;
    }

    /**
     * @return float
     */
    public function getRpai(): float
    {
        return $this->rpai;
    }

    /**
     * @param float $rpai
     */
    public function setRpai(float $rpai)
    {
        $this->rpai = $rpai;
    }

    /**
     * @return float
     */
    public function getVri(): float
    {
        return $this->vri;
    }

    /**
     * @param float $vri
     */
    public function setVri(float $vri)
    {
        $this->vri = $vri;
    }

    /**
     * @return float
     */
    public function getTensionIndex(): float
    {
        return $this->tensionIndex;
    }

    /**
     * @param float $tensionIndex
     */
    public function setTensionIndex(float $tensionIndex)
    {
        $this->tensionIndex = $tensionIndex;
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
            'amo' => $this->amo,
            'ti' => $this->ti,
            'ivb' => $this->ivb,
            'rpai' => $this->rpai,
            'vri' => $this->vri,
            'tension_index' => $this->tensionIndex,
        ];
    }
}
