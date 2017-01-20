<?php

class ApiViewDiseaseNameV7 extends EApiViewService {
    public function __construct($values) {
        parent::__construct();
        $this->disease_name = isset($values['disease_name']) ? $values['disease_name'] : null;
    }

    protected function loadData() {
        $this->loadDisease();
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
    public function loadDisease(){
        $disease = new Disease();
        $data = new stdClass();
        if(!empty($this->disease_name)){
            $model = $disease->getByName($this->disease_name, 7);
            if (isset($model)) {
                $data->id = $model->getId();
                $data->name = $model->getName();
                $categoryDiseaseJoin = CategoryDiseaseJoin::model()->getByAttributes(array('disease_id'=>$model->getId()));
                $data->subCatId = $categoryDiseaseJoin->getSubCatId();
                $diseaseCategory = DiseaseCategory::model()->getByAttributes(array('sub_cat_id'=>$data->subCatId, 'app_version'=>7));
                $data->subCatName = $diseaseCategory->getSubCategoryName();
                $data->catId = $diseaseCategory->getCategoryId();
                $data->catName = $diseaseCategory->getCategoryName();
             
            }
        }
        $this->setDisease($data);
    }
    private function setDisease($data){
        $this->results = $data;
    }

}
