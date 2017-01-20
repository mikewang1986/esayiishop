<?php

class ApiViewDoctorSearchV7 extends EApiViewService {

    private $searchInputs;      // Search inputs passed from request url.
    private $getCount = false;  // whether to count no. of Doctors satisfying the search conditions.
    private $pageSize = 12;
    private $doctorSearch;  // DoctorSearch model.
    private $doctors;
    private $doctorCount;     // count no. of Doctors.
    private $doctorsCityList;
   //加入医生全部城市查询
    private $doctorsCitySearch;
    private $searchcityInputs;

    public function __construct($searchInputs) {
        parent::__construct();
        $this->searchInputs = $searchInputs;
       //add by wanglei
        $this->searchcityInputs=$searchInputs;
        $this->getCount = isset($searchInputs['getcount']) && $searchInputs['getcount'] == 1 ? true : false;
        $this->searchInputs['pagesize'] = isset($searchInputs['pagesize']) && $searchInputs['pagesize'] > 0 ? $searchInputs['pagesize'] : $this->pageSize;
        if(isset($this->searchInputs['city'])){
            if($this->searchInputs['city'] == 0){
                unset($this->searchInputs['city']);
            }
        }
        $this->doctorSearch = new DoctorSearchV7($this->searchInputs);
        $this->doctorSearch->addSearchCondition("t.date_deleted is NULL");
        //加入医生全部城市查询
        $this->doctorsCitySearch = new DoctorCitySearch($this->searchcityInputs);
        $this->doctorsCitySearch->addSearchCondition("t.date_deleted is NULL");
    }

    protected function loadData() {
        // load Doctors.
       
        $this->loadDoctors();
        $this->loadDoctorsCity();
        $this->loadDoctorsCityList();
        if ($this->getCount) {
            $this->loadDoctorCount();
        }
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'dataNum' => $this->doctorCount,
                'dataCity' => $this->doctorsCityList,
                'results' => $this->doctors,
            );
        }
    }

    private function loadDoctors() {
        if (is_null($this->doctors)) {
            $models = $this->doctorSearch->search();
            if (arrayNotEmpty($models)) {
                $this->setDoctors($models);
            }
        }
    }

    private function setDoctors(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->isServiceId = null;
            $bookingServiceJoin = BookingServiceDoctorJoin::model()->getByDoctorIdAndBookingServiceId($model->getId(), BookingServiceConfig::BOOKING_SERVICE_FREE_LIINIC);
            if (isset($bookingServiceJoin)) {
                $data->isServiceId = BookingServiceConfig::BOOKING_SERVICE_FREE_LIINIC;
            }
            $data->name = $model->getName();
            $data->mTitle = $model->getMedicalTitle();
            $data->aTitle = $model->getAcademicTitle();
            // $data->hospital = $model->getHospitalId();
            $data->hpName = $model->getHospitalName();
            $data->hpDeptId = $model->getHpDeptId();
            $data->hpDeptName = $model->getHpDeptName();
            $data->desc = $model->getDescription();
            $data->imageUrl = $model->getAbsUrlAvatar();
            //    $data->actionUrl = Yii::app()->createAbsoluteUrl('/mobile/booking/create', array('did' => $data->id, 'header' => 0, 'footer' => 0));   // @used by app.
            $data->actionUrl = $data->actionUrl = Yii::app()->createAbsoluteUrl('/api/booking');    // @user by app.
            $data->isContracted = $model->getIsContracted();
            $data->reasons = $model->getReasons();
            $data->isExpteam = $model->getIsExpteam();
           // $this->doctorsCityList[] = $model->getCityId();
            $this->doctors[] = $data;
        }
   
    }
     private function loadDoctorsCity() {
            $models = $this->doctorsCitySearch->search();
            if (arrayNotEmpty($models)) {
                $this->setDoctorsCity($models);
            }

    }

    private function setDoctorsCity(array $models) {
        foreach ($models as $model) {
            $datacity = new stdClass();
            $this->doctorsCityList[] =$model->getCityId();
        }
   
    }
    private function loadDoctorCount() {
        if (is_null($this->doctorCount)) {
            $count = $this->doctorSearch->count();
            $this->setCount($count);
        }
    }

    private function setCount($count) {
        $this->doctorCount = $count;
    }
    
    private function loadDoctorsCityList() {
       $doctorCityList = $this->doctorsCityList;
       if (arrayNotEmpty($doctorCityList)) {
                $this->setDoctorCityList($doctorCityList);
       }
    }
    
    private function setDoctorCityList($doctorCityList) {
        $cityList = array_unique($doctorCityList);
        unset($this->doctorsCityList);
        foreach ($cityList as $k=>$v) {
           $model = RegionCity::model()->getByAttributes(array('id'=> $v));
           if(isset($model)){
               $cityobj=new stdClass();
               $cityobj->id=  $model->id;
               $cityobj->name =  $model->name;
               $this->doctorsCityList[] = $cityobj;
           }
        }
      
        
    }

}
