<?php

class ApiViewCityV15 extends EApiViewService {
    private $has_team;
    public $type;
    
    public function __construct($values) {
        parent::__construct();
        $this->has_team = isset($values['has_team']) ? $values['has_team'] : null;
        $this->type = isset($values['type']) ? $values['type'] : null;
        $this->results = new stdClass();
    }

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
    public function loadCity(){
        $data = array();
        if(isset($this->has_team) && $this->has_team == 1){
            $cities = CityListDoctor::model()->getCityHasTeam();
        }else{
            if($this->type == 'hospital'){
                $cities = CityListHpDept::model()->getAllCityByHospital();
            }else{
                $cities = CityListDoctor::model()->getAllCityByDoctor();
            }
            
        }
//         $cities = (isset($this->has_team) && $this->has_team == 1) ? CityListDoctor::model()->getCityHasTeam() : CityListDoctor::model()->getAllCity();
        foreach($cities as $city)
        {
//             if($city->is_hot){
//                 $city->state_id = 1;
//                 $city->state_name = '热门城市';
//             }
            $isExist = 0;
            foreach($data as $k=>$v){
                if(isset($v['id']) && $v['id'] == $city->state_id){
                    $data[$k]['subCity'][]  = array('id'=>$city->city_id, 'city'=>$city->city_name);
                    $isExist = 1;
                    break;
                }
            }
            if($isExist == 0){
//                 $data[] = array('id'=>$city->state_id, 'state'=>$city->state_name, 'subCity'=>array(array('id'=>$city->city_id, 'city'=>$city->city_name)));
                $data[] = array('id'=>$city->city_id, 'city'=>$city->city_name,'is_hot'=>$city->is_hot);
            }
        }
        $this->setCity($data);
    }
    private function setCity($data){
        $this->results = $data;
    }

}
