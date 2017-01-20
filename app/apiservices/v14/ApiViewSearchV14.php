<?php

class ApiViewSearchV14 extends EApiViewService {

    private $searchInputs;      // Search inputs passed from request url.
    private $getCount = false;  // whether to count no. of Doctors satisfying the search conditions.
    private $pageSize = 100;
    private $doctorSearch;  // DoctorSearch model.
    private $diseaseSearch;  // DiseaseSearch model.
    private $doctors;
    private $doctorCount;     // count no. of Doctors.
    private $diseases;
    private $diseaseCount;     // count no. of Diseases.
    
    private $hospitalSearch;
    private $hospitals;
    private $hospitalCount;    // count no. of Hospital.

    public function __construct($searchInputs) {
        parent::__construct();
        $this->searchInputs = $searchInputs;
        $this->getCount = isset($searchInputs['getcount']) && $searchInputs['getcount'] == 1 ? true : false;
        $this->searchInputs['pagesize'] = isset($searchInputs['pagesize']) && $searchInputs['pagesize'] > 0 ? $searchInputs['pagesize'] : $this->pageSize;
        $this->doctorSearch = new DoctorSearchV8($this->searchInputs);
        $this->doctorSearch->addSearchCondition("t.date_deleted is NULL");
        $this->diseaseSearch = new DiseaseSearch($this->searchInputs);
        $this->diseaseSearch->addSearchCondition("t.date_deleted is NULL");
        $this->hospitalSearch = new HospitalSearch($this->searchInputs);
        $this->hospitalSearch->addSearchCondition("t.is_show = 1");
        $this->hospitalSearch->addSearchCondition("t.date_deleted is NULL");
        
    }

    protected function loadData() {
        // load Doctors.
        $this->loadDoctors();
        $this->loadDiseases();
        $this->loadHospitals();
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

    private function loadDoctors() {
        if (is_null($this->doctors)) {
            $key = md5($this->searchInputs['name']."doctors31");
            $models=Yii::app()->cache->get($key);
            if(!$models){
                $models = $this->doctorSearch->search();
                $value = $models;
                $expire = 86400;
                yii::app()->cache->set($key, $value, $expire);
            }
            if (arrayNotEmpty($models)) {
                $this->setDoctors($models);
            }
        }
    }

    private function loadDiseases() {
        if (is_null($this->diseases)) {
            $key = md5($this->searchInputs['name']."diseases31");
            $models=Yii::app()->cache->get($key);
            if(!$models){
                $models = $this->diseaseSearch->search();
                $value = $models;
                $expire = 86400;
                yii::app()->cache->set($key, $value, $expire);
            }
            if (arrayNotEmpty($models)) {
                $this->setDiseases($models);
            }
        }
    }

    private function loadHospitals() {
        if (is_null($this->hospitals)) {
            $key = md5($this->searchInputs['name']."hospitals31");
            $models=Yii::app()->cache->get($key);
            if(!$models){
                $models = $this->hospitalSearch->search();
                $value = $models;
                $expire = 86400;
                yii::app()->cache->set($key, $value, $expire);
            }
            if (arrayNotEmpty($models)) {
                $this->setHospitals($models);
            }
        }
    }
    
    private function setDoctors(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->name = $model->getName();
            $data->hpName = $model->getHospitalName();
            $data->hpDeptName = $model->getHpDeptName();
            $data->desc = $model->getDescription();
            $data->imageUrl = $model->getAbsUrlAvatar();
            $data->actionUrl = $data->actionUrl = Yii::app()->createAbsoluteUrl('/api/booking');    // @user by app.
            $data->isContracted = $model->getIsContracted();
            $data->mTitle = $model->getMedicalTitle();
            $data->aTitle = $model->getAcademicTitle();
            $this->results->doctors[] = $data;
        }
    }

    private function setDiseases(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->name = $model->getName();
            $this->results->diseases[] = $data;
        }
    }
    
    private function setHospitals(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->name = $model->getName();
            $this->results->hospitals[] = $data;
        }
    }

}
