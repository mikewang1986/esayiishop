<?php

class ApiViewDiagnosisDoctor extends EApiViewService {
    public $searchInputs;
    public $doctorSearch;
    public $doctorIds;
    
    public function __construct($value) {
        $this->searchInputs = $value;
        
        
        parent::__construct();
    }

    protected function loadData() {
        $this->loadDoctors();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->results,
            );
        }
    }

    public function loadDoctors() {
        $this->loadDoctorIds();
        $doctorList = $this->doctorIds;
        $tt= $this->searchInputs;
        $tt['id'] = $doctorList;
        $this->doctorSearch = new DoctorSearchV7($tt);
        if($this->searchInputs['citys']){
            $cityArray = array(1,73,200,254,186);
            if(in_array($this->searchInputs['citys'], $cityArray)){
                $this->doctorSearch->criteria->addCondition('t.city_id ='.$this->searchInputs['citys']);
            }else{
                $this->doctorSearch->criteria->addNotInCondition('t.city_id', $cityArray);
            }
        }
        $this->doctorSearch->criteria->addInCondition('t.id',$doctorList);
        $models = $this->doctorSearch->search();
        if (arrayNotEmpty($models)) {
             $this->setDoctors($models);
        }
    }
    
    

    private function setDoctors($models) {
        $temp = array();
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->name = $model->getName();
            $data->hpId = $model->getHospitalId();
            $data->hpName = $model->getHospitalName();
            $data->mTitle = $model->getMedicalTitle();
            $data->aTitle = $model->getAcademicTitle();
            $data->imageUrl = $model->getAbsUrlAvatar();
            $data->desc = $model->getDescription();
            $data->hpDeptId = $model->getHpDeptId();
            $data->hpDeptName = $model->getHpDeptName();
            $data->isContracted = $model->getIsContracted();
            $bookingServiceJoin = BookingServiceDoctorJoin::model()->getByDoctorIdAndBookingServiceId($model->getId(), BookingServiceConfig::BOOKING_SERVICE_FREE_LIINIC);
            if (isset($bookingServiceJoin)) {
                $data->isServiceId = BookingServiceConfig::BOOKING_SERVICE_FREE_LIINIC;
            }
            $temp[] = $data;
        }  
        $this->results->page[] = $temp;
    }


    

    public function getDiagnosisDoctors(){
        return array( 
            3107,
            3112,
            3081,
            3084,
            3085,
            3086,
            3088,
            3007,
            949,
            2979,
            3148,
            3149,
            3178,
            3182,
            3183,
            3186,
            3189,
            3191,
            137,
            3046,
            1030,
            3061,
            3111,
            3059,
            3057,
            3110,
            3108,
            3109,
            3093,
            3094,
            3095,
            3167,
            3017,
            3047,
            3045,
            3055,
            3018,
            3064,
            3237,
            3223,
            2992,
            1427,
            3009,
            3217,
            1784,
            3023,
            2980,
            3051,
            2907,
            2906,
            3218,
            3075,
            3244,
            3227,
            3229,
            3234,
            3162,
            3172,
            3169,
            3193,
            3196,
            3204,
            68,
            3024,
            3175,
            65,
            1192,
            1286,
            3101,
            3104,
            3200,
            3202,
            486,
            3026,
            3100,
            3025,
            3030,
            3031,
            2883,
            290,
            130,
            1296,
            359,
            3050,
            3049,
            3173,
            3179,
            1336,
            3097,
            3035,
            3041,
            3034,
            3042,
            3043,
            3040,
            3033,
            3053,
            3232,
            662,
            700,
            3125,
            3126,
            3124,
            3231,
            3129,
            3128,
            3130,
            3134,
            3087,
            2929,
            3067,
            3060,
            3165,
            3146,
            3078,
            1887,
            3180,
            3170,
            3181,
            3185,
            1177,
            2999,
            270,
            3106,
            3014,
            3096,
            3015,
            3120,
            3121,
            3127,
            3238,
            3089,
            3147,
            3160,
            3190,
            3044,
            170,
            455,
            1010,
            2957,
            3016,
            3177,
            3219,
            1624,
            1750,
            3077,
            3187,
            3224,
            1357,
            3003,
            3102,
            3105,
            3004,
            3138,
            3038,
            3054,
            3122,
            3123,
            3131,
            3090,
            3069,
            3228,
            3168,
            3013,
            3153,
            3133,
            3132,
            3065,
            3220,
            3221,
            3222,
            3079,
            3071,
            3184,
            3198,
            3163,
            3174,
            3027,
            3028,
            3164
            );
    }

    private function loadDoctorIds() {
        $bookingServiceDoctorJoins = BookingServiceDoctorJoin::model()->getAllByAttributes(array('booking_service_id' => BookingServiceConfig::BOOKING_SERVICE_FREE_LIINIC));
        foreach ($bookingServiceDoctorJoins as $value) {
            $this->doctorIds[] = $value->doctor_id;
        }
    }
}
