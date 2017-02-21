<?php
namespace app\apiservices\api;
use Yii;
use app\apiservices\EApiViewService;
use app\models\DiseaseManager;
use app\components\ErrorCode;
class ApiViewDiseaseCategory extends EApiViewService {
    public function __construct() {
        parent::__construct();
    }
    protected function loadData() {
        $this->loadDiseaseCategory();
    }
    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
               // 'status' => self::RESPONSE_OK,
                'errorCode' => ErrorCode::ERROR_NO,
                'errorMsg' => ErrorCode::getErrText(ErrorCode::ERROR_NO),
                'results' => $this->results,
            );
        }
    }
    public function loadDiseaseCategory(){
        $disMgr = new DiseaseManager();
        $models = $disMgr->loadDiseaseCategoryListV8();//新增12个科室

        $navList = array();
        foreach ($models as $model) {
            $data = new \stdClass();
            $data->id = $model->getCategoryId();
            $data->name = $model->getCategoryName();
            // sub group.
            $subGroup = new \stdClass();
            $subGroup->id = $model->getSubCategoryId();
            $subGroup->name = $model->getSubCategoryName();
            if (isset($navList[$data->id])) {
                $navList[$data->id]->subCat[] = $subGroup;
            } else {

                $navList[$data->id] = $data;
                $navList[$data->id]->subCat[] = $subGroup;
            }
        }
        $this->setDiseaseCategory(array_values($navList));
    }
    private function setDiseaseCategory($data){
        $this->results = $data;
    }

}
