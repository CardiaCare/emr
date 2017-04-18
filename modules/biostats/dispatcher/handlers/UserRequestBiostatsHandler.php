<?php

namespace app\modules\biostats\dispatcher\handlers;

use app\modules\biostats\models\Biostats;
use app\modules\emr\models\Biosignal;
use app\modules\emr\models\Patient;
use app\modules\biostats\models\UserRequestBiostats;
use app\modules\biostats\dispatcher\BiostatsHandlerInterface;
use app\modules\biostats\dispatcher\request\BiostatsRequest;
use app\modules\biostats\dispatcher\request\UserRequestBiostatsRequest;

class UserRequestBiostatsHandler implements BiostatsHandlerInterface {

    /**
     * @var UserRequestBiostatsRequest
     */
    //protected $request;
    /**
     * @param BiostatsRequest $request
     * @return Biostats
     */
    public function handle(BiostatsRequest $request): Biostats {
        if (!($request instanceof UserRequestBiostatsRequest)) {
            throw new \InvalidArgumentException();
        }
        //$this->$request = $request;
        return $this->createBiostats($request);
    }

    private function searchRR($signal) {

        // stores index of signal's points, that higher THRESHOLD
        //search average point in signal
        // init RPeaks by false values
        $sum = 0;
        $RPeaks = array();
        for ($i = 0; i < count($signal); $i++) {
            array_push($RPeaks, false);
            $sum += $signal[$i];
        }

        $avg = $sum / $signal . count();

        //search min point in signal
        $min = 255;
        for ($i = 0; i < count($signal); $i++) {
            if ($signal[$i] < $min) {
                $min = $signal[$i];
            }
        }
        //get THRESCHOLD
        $threshold = $avg - ($avg - $min) * 3 / 5;

        $j = 0;
        $minPoint = 0;
        $minIndex = 0;
        $seriesFirst = 0;
        $seriesLast = 0;
        $Peaks = [];
        for ($i = 0; i < count($signal); $i++) {
            if ($signal[$i] < $threshold) {
                array_push($Peaks, $i);
            }
        }

        //find a local min
        while ($j < count($Peaks)) {
            $seriesFirst = $j;
            $seriesLast = $j;

            if ($seriesLast < count($Peaks) - 1) {
                //search for series of points indexes above THRESHOLD
                while (($Peaks[$seriesLast] == ($Peaks[$seriesLast + 1] - 1)) 
                        & ($seriesLast < (count($Peaks) - 2))) {
                    $seriesLast++;
                }
                //search peaks
                if ($seriesLast > $seriesFirst) {
                    //search local min
                    $minPoint = $signal[$seriesFirst];
                    $minIndex = $seriesFirst;
                    for ($k = $seriesFirst; $k <= $seriesLast; $k++) {
                        if ($signal[$Peaks[$k]] < $minPoint) {
                            $minPoint = $signal[$Peaks[$k]];
                            $minIndex = $k;
                        }
                    }
                    $RPeaks[$Peaks[$minIndex]] = true;
                }
            }
            $j = $seriesLast + 1;
        }
        return $RPeaks;
    }

    private function getRRType($RRInterval) {
        if ((60.0 / $RRInterval < 80.0) & (60.0 / $RRInterval > 60.0)) {
            return "Normal";
        } else
        if ((60.0 / $RRInterval < 60.0) & (60.0 / $RRInterval > 20.0)) {
            return "Bradycardia";
        } else
        if (60.0 / $RRInterval < 300.0) {
            return "Tachycardia";
        }
        return "Exeption";
    }

    private function getRRIntervals($RPeaks) {
        //search RR-intervals
        $RRIntervals = array();
        for ($i = 0; $i < count($RPeaks) - 1; $i++) {
            $rr = ($RPeaks[i + 1] - $RPeaks[i]) / 300.0;
            array_push($RRIntervals, $rr);
        }
        return $RRIntervals;
    }

    private function getPulse($RRIntervals) {
        $HeartRate = array();
        for ($i = 0; $i < size($RRIntervals); $i++) {
            array_push($HeartRate, round(60.0 / $RRIntervals[$i]));
        }

        return $HeartRate;
    }

    private function NN50Histogram($RR) {
        $NN50 = 0.0;
        for ($i = 0; $i < (count($RR) - 1); $i++){
        //0,5 -> 50 ms
            if ( ($RR[$i+1].timestamp - $RR[$i].timestamp) > 0.5){
                $NN50++;
            }
        }

        return $NN50;
    }

    private function pNN50Histogram($RR) {
        return ($this->NN50Histogram($RR) / count($RR));
    }

    private function RMSSDHistogram($RR) {
        $RMSSD = 0.0;

        for ($i = 0; $i < (count($RR) - 1); $i++){
            $RMSSD += pow($RR[$i + 1] . timestamp - $RR[$i] . timestamp, 2);
        }

        $RMSSD /= (count($RR) - 1);
        $RMSSD = sqrt($RMSSD);

        return $RMSSD;
    }

    private function SDNNHistogram($histogram) {
        $SDNN = 0.0;
        $MatOj = 0.0;

        for ($i = 0; $i < count($histogram); $i++){
            $MatOj = $histogram[$i] + $MatOj;
        }
        $MatOj = $MatOj / count($histogram);

        for ($i = 0; $i < count($histogram); $i++){
            $SDNN = pow(($histogram[i] - $MatOj), 2) + $SDNN;
        }
        $SDNN = $SDNN / (count($histogram) - 1);
        return $SDNN;
    }
    private function moHistogram($histogram, $RR) {
        $max = $histogram[0];

        for ($i = 1; $i < count($RR); $i++) {
            if ($RR[$i] . timestamp > $max){
                $max = $histogram[i];
            }
        }
        return $max;
    }
        
    private function amoHistogram($histogram, $RR) {
        return $this->moHistogram($histogram, $RR) / count($RR);
    }


    private function VARHistogram($RR) {
        $max = $RR[0] . timestamp;
        $min = $RR[0] . timestamp;

        for ($i = 1; $i < count(RR); $i++) {
            if ($RR[$i] . timestamp > $max) {
                $max = $RR[$i] . timestamp;
            } else {
                if ($RR[$i].timestamp < $min) {
                    $min = $RR[$i].timestamp;
                }
            }
        }

        return ($max - $min);
    }

    private function PAPRHistogram($histogram, $RR) {
        return $this->amoHistogram($histogram, $RR) / $this->moHistogram($histogram, $RR);
    }

    /**
     * @return UserRequestBiostats
     */
    private function createBiostats(UserRequestBiostatsRequest $request)
    {
        
        $patient = $request->getPatient();
        
        $ecg = Biosignal::find()->byId(938)->byPatientId($patient->getPrimaryKey())->one();

        if ($ecg == null) {
            throw new NotFoundHttpException();
        }
                
        $data = $ecg->getData();
        
        //$RPeaks = $this->searchRR($data);
        
//        $RRIntervals = $this->getRRIntervals($RPeaks);
//        
//        $HeartRate = $this->getPulse($RRIntervals);
//        
//        $sum = 0;
//        foreach ($HeartRate as $dot ){
//           $sum += $dot;
//        }   
//        $pulse = $sum/$HeartRate.count();
        $pulse = 80.0;
                
        $biostats = new UserRequestBiostats();
        $biostats->setHeartRate($pulse);
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
