<?php

class ApiViewHospitalWapSearch extends EApiViewService {

    private $searchInputs;
    private $getCount = false;    // whether to count the total no. of Hospitals satisfying the search conditions.
    private $pageSize = 10;
    private $hospitalSearch;
    private $hospitals;
    private $locations;
    private $cityId;
    private $currentLocation;
    private $count;

    public function __construct($searchInputs) {
        parent::__construct();
        $this->searchInputs = $searchInputs;
        $this->searchInputs['is_show'] = 1;
        $this->getCount = isset($searchInputs['getcount']) && $searchInputs['getcount'] == 1 ? true : false;
        $this->searchInputs['pagesize'] = isset($searchInputs['pagesize']) && $searchInputs['pagesize'] > 0 ? $searchInputs['pagesize'] : $this->pageSize;
        $this->hospitalSearch = new HospitalSearchV15($this->searchInputs);
    }

    /*
     * @version  1.0
     * @author   plus.wu
     * @date     2017.1.13
     * @remarks  load Date.
     */
    protected function loadData() {
        $this->loadHospitals();
        if ($this->getCount) {
            $this->loadCount();
        }
    }

    /*
     * @version  1.0
     * @author   plus.wu
     * @date     2017.1.13
     * @remarks  load count.
     */
    private function loadCount() {
        if (is_null($this->count)) {
            $count = $this->hospitalSearch->count();
            $this->count = $count;
        }
    }
    
    /*
     * @version  1.0
     * @author   plus.wu
     * @date     2017.1.13
     * @remarks  load output Data.
     */
    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'dataNum' => $this->count,
                'results' => $this->hospitals,
            );
        }
    }

    /*
     * @version  1.0
     * @author   plus.wu
     * @date     2017.1.13
     * @remarks  Load Hospital.
     */
    private function loadHospitals() {
        if (is_null($this->hospitals)) {
            $this->hospitals = array();
            $hospitals = $this->hospitalSearch->search();
            if (arrayNotEmpty($hospitals)) {
                $this->setHospitals($hospitals);
            }
        }
    }
    
    /*
     * @version  1.0
     * @author   plus.wu
     * @date     2017.1.13
     * @remarks  Set Hospital value into Data.
     */
    private function setHospitals(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->id;
            $hospital = $model->getHospital();
            $data = new stdClass();
            $data->hospital_id = $hospital->getId();
            $data->name = $hospital->getName();
            $data->imageUrl = $hospital->getAbsUrlAvatar();
            $data->hpClass = $hospital->getClass();
            
            $hospitalDept = $model->getHpDept();
            $data->hp_dept_id = $model->hp_dept_id;
            $data->hp_dept_name = $hospitalDept->getName();
            $data->hp_dept_desc = $hospitalDept->getDescription();
            $data->hp_dept_id = $model->hp_dept_id;
            $data->hospital_id = $model->hospital_id;
            $data->category_id = $model->category_id;
            $data->category_name = $model->category_name;
            $data->rank = $model->rank;
            $this->hospitals[] = $data;
        }
    }
    
}
