<?php

class ApiViewCity extends EApiViewService {
    private $city;
    public function __construct($id) {
        parent::__construct();
        $this->city = $id;
    }
    /**
     * loads data by the given $id (Disease.id).
     * @param integer $diseaeId Disease.id     
     */
    protected function loadData() {

        $this->loadCity();
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

    /**
     *
     * @throws CException
     */
    private function loadCity() {

        $model = CityListDoctor::model()->getByAttributes(array('city_id'=>$this->city));
        if (is_null($model)) {
            $this->throwNoDataException();
        }
        $this->setCity($model);

    }

    private function setCity(CityListDoctor $model) {
        $data = new stdClass();
        $data->id = $model->getCityId();
        $data->name = $model->getCityName();
        $this->results = $data;
    }
}
