<?php

class ApiViewHospitalDeptV11 extends EApiViewService {

    private $deptId;
    private $department;
    private $doctors;
    private $queryOptions;

    public function __construct($deptId, $queryOptions = null) {
        parent::__construct();
        $this->deptId = $deptId;
        $this->queryOptions = $queryOptions;
        $this->results = new stdClass();
    }

    protected function loadData() {
        // Load hospital department by $deptId.
        $this->loadDepartment();
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

    private function loadDepartment() {
        if (is_null($this->department)) {
            $hospitalMgr = new HospitalManager();
            $with = array('hpDeptHospital');
            $dept = $hospitalMgr->loadHospitalDeptById($this->deptId, $with);
            if (isset($dept)) {
                $this->setDepartment($dept);
            }
        }
    }

    private function setDepartment(HospitalDepartment $model) {
        $data = new stdClass();
        $data->id = $model->getId();
        $data->name = $model->getName();
        $hospital = $model->getHospital();
        if (isset($hospital)) {
            $data->hpName = $hospital->getName();
        } else {
            $data->hpName = '';
        }
        $data->description = $model->getDescription();
        $data->position = $model->position;
        $data->scale = $model->scale;
        $data->specialty = $model->specialty;
        $data->strength = $model->strength;
        if($model->honor){
            $data->honor = array_values(explode('#', $model->honor));
        }else{
            $data->honor = array();
        }
        $this->results->department = $data;
    }


}
