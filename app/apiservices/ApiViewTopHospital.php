<?php

class ApiViewTopHospital extends EApiViewService {
    private $pageSize = 20;
    private $tophospitals;
    private $options;
    
    public function __construct($values) {
           parent::__construct();
           if(empty($values['page'])){
               $values['page'] = 1;
           }
           $page = isset($values['page'])?  $values['page']:1;
           $limit=($page-1)*$this->pageSize;
           $this->options=array('limit'=>$this->pageSize,'offset'=>$limit);
           
       
    }

    protected function loadData() {
        $this->loadTopHospitals();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->tophospitals,
            );
        }
    }

    private function loadTopHospitals() {
            $topHospitals = array();
            $topHospitals = TopHospital::model()->getAll($with = null,$this->options);
            if (arrayNotEmpty($topHospitals)) {
                $this->setTopHospitals($topHospitals);
            }
    }
    
    private function setTopHospitals(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->hospital_id = $model->getHospitalId();
            $data->hospital_name = $model->getHospitalName();
            $data->sort = $model->getSort();
            $this->tophospitals[] = $data;
        }
        
    }
    

}