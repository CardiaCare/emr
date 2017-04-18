<?php

namespace app\modules\biostats\dispatcher\handlers;

use app\modules\biostats\models\Biostats;
use app\modules\emr\models\Biosignal;
use app\modules\emr\models\Patient;
use app\modules\biostats\models\UserRequestBiostats;
use app\modules\biostats\dispatcher\BiostatsHandlerInterface;
use app\modules\biostats\dispatcher\request\BiostatsRequest;
use app\modules\biostats\dispatcher\request\UserRequestBiostatsRequest;

class UserRequestBiostatsHandler implements BiostatsHandlerInterface
{
    /**
     * @var UserRequestBiostatsRequest
     */
    //protected $request;
    /**
     * @param BiostatsRequest $request
     * @return Biostats
     */
    public function handle(BiostatsRequest $request): Biostats
    {
        if (!($request instanceof UserRequestBiostatsRequest)) {
            throw new \InvalidArgumentException();
        }
        //$this->$request = $request;
        return $this->createBiostats($request);
    }

       
    private function searchRR($signal){

    // stores index of signal's points, that higher THRESHOLD


    //search average point in signal
    // init RPeaks by false values
    $sum = 0;
    $RPeaks  = array();
    for ($i = 0; i < count($signal); $i++ ){
        array_push($RPeaks,false);
        $sum += $signal[$i];
    }
    
    $avg = $sum/$signal.count();


    //search min point in signal
    $min = 255;
    for ($i = 0; i < count($signal); $i++ ){
        if ($signal[$i]  < $min){
            $min = $signal[$i];
        }
    }

    //get THRESCHOLD
    $threshold = $avg - ($avg-$min)*3/5;
   
    $j = 0;
    $minPoint = 0;
    $minIndex = 0; $seriesFirst = 0; $seriesLast = 0;
    $Peaks = [];
    for ($i = 0; i < count($signal); $i++ ){
        if($signal[$i] < $threshold ){
            array_push($Peaks, $i);
        }
    }

    //find a local min
    while( $j < count($Peaks)){
        $seriesFirst = $j;
        $seriesLast = $j;

        if ($seriesLast < count($Peaks)-1) {

            //search for series of points indexes above THRESHOLD
            while(($Peaks[$seriesLast] == ($Peaks[$seriesLast+1]-1)) & ($seriesLast < (count($Peaks)-2))){
                $seriesLast++;
            }

            //search peaks
            if ($seriesLast > $seriesFirst){
                //search local min
                $minPoint = $signal[$seriesFirst];
                $minIndex = $seriesFirst;
                for ($k = $seriesFirst; $k <= $seriesLast; $k++ ){
                    if ($signal[$Peaks[$k]] < $minPoint){
                        $minPoint = $signal[$Peaks[$k]];
                        $minIndex = $k;
                    }
                }

//                RPeaks.push_back(Peaks[minIndex]);
                $RPeaks[$Peaks[$minIndex]] = true;
            }
        }
        $j = $seriesLast + 1;
    }
    return $RPeaks;
}

private function getRRType($RRInterval){
    if ((60.0/$RRInterval < 80.0) & (60.0/$RRInterval > 60.0))
        return "Normal";
    else
        if ((60.0/$RRInterval < 60.0) & (60.0/$RRInterval > 20.0))
            return "Bradycardia";
        else
            if (60.0/$RRInterval < 300.0)
                return "Tachycardia";
    return "Exeption";
}

private function getRRIntervals($RPeaks){
    //search RR-intervals
    $RRIntervals = array();
    for ($i = 0; $i < count($RPeaks)-1; $i++ ){
        $rr = ($RPeaks[i+1]-$RPeaks[i])/ 300.0;
        array_push($RRIntervals, $rr);
        //qDebug() <<  rr <<" RR-interval\n";
    }
    return $RRIntervals;
}

private function getPulse($RRIntervals){
    $HeartRate = array();
    for ($i = 0; $i < size($RRIntervals); $i++ ){
        array_push($HeartRate, round(60.0/$RRIntervals[$i]));
    }
    
    return $HeartRate;
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
        
        $RPeaks = $this->searchRR($data);
        
        $RRIntervals = $this->getRRIntervals($RPeaks);
        
        $HeartRate = $this->getPulse($RRIntervals);
        
        $sum = 0;
        foreach ($HeartRate as $dot ){
           $sum += $dot;
        }   
        $pulse = $sum/$HeartRate.count();
                
        $biostats = new UserRequestBiostats();
        $biostats->setUser($patient->getPrimaryKey());
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
