<?php

class ApiViewArea extends EApiViewService {

    public function __construct() {
        parent::__construct();
    }

    /**
     * loads data by the given $id (Disease.id).
     * @param integer $diseaeId Disease.id
     */
    protected function loadData() {

        $this->loadArea();
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
    private function loadArea() {
        $datas = array(
            array(
                'area_id'=>0,
                'area_name'=>'全部地区',
                'description'=>'',
                'coverage'=>''
            ),
            array(
                'area_id'=>1,
                'area_name'=>'东北区',
                'description'=>'含黑龙江、吉林、辽宁',
            ),
            array(
                'area_id'=>2,
                'area_name'=>'华北区',
                'description'=>'含北京、天津、河北、山西、内蒙古',
            ),
            array(
                'area_id'=>3,
                'area_name'=>'华东区',
                'description'=>'含上海、浙江、江苏、安徽、福建、山东、江西',
            ),
            array(
                'area_id'=>4,
                'area_name'=>'华南区',
                'description'=>'含广东、广西、海南',
            ),
            array(
                'area_id'=>5,
                'area_name'=>'华中区',
                'description'=>'含河南、湖北、湖南',
            ),
            array(
                'area_id'=>6,
                'area_name'=>'西北区',
                'description'=>'含甘肃、宁夏、青海、陕西、新疆',
            ),
            array(
                'area_id'=>7,
                'area_name'=>'西南区',
                'description'=>'含贵州、四川、重庆、云南',
            ),

        );

        $this->setArea($datas);

    }

    private function setArea($datas) {
        $this->results = $datas;
    }
}


