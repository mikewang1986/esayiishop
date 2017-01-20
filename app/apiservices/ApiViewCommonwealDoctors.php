<?php

class ApiViewCommonwealDoctors extends EApiViewService {

    public function __construct() {
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
        $doctorList = include dirname(__FILE__) . '/../config/commonwealdoctors.php';
        foreach ($doctorList as $doctorIds) {
            $criteria = new CDbCriteria();
            $criteria->addCondition('t.date_deleted is NULL');
            $criteria->addInCondition('t.id', $doctorIds);
            $models = Doctor::model()->findAll($criteria);
            if (arrayNotEmpty($models)) {
                $this->setDoctors($models);
            }
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
            $temp[] = $data;
        }
        $this->results->page[] = $temp;
    }

}
