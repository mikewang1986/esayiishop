<?php
class ApiViewExpertsShow extends EApiViewService {
    private $doctorSearch;  // DoctorSearch model.
    private $doctors;
    private $doctorsids;
    private $doctorsdesc;
    private $key="exportsshow_token";
    public function __construct($searchInputs) {
        $this->searchInputs = $searchInputs;
        $this->doctorSearch = new DoctorSearch($this->searchInputs);
        $doctorcontent=Yii::app()->params['doctorcase']['doctor'];
        $doctorstr=implode(",",$this->set_docotrsid($doctorcontent));
        $this->doctorSearch->addSearchCondition("t.date_deleted is NULL");
        $this->doctorSearch->addSearchCondition("t.id in (".$doctorstr.")");
        parent::__construct();
    }
    /**
     * loads data by the given $id (Disease.id).
     * @param integer $diseaeId Disease.id     
     */
    protected function loadData() {
        $ttdoctors = Yii::app()->cache->get($this->key);
        if(empty($ttdoctors)){
            // load Doctors.
           $this->loadDoctors();
        }
        else{
            $this->doctors=$ttdoctors;
        }
    }
    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output=new stdClass();
            $this->output->status=self::RESPONSE_OK;
            $this->output->doctors=$this->doctors;
        }
    }

    /**
     *
     * @throws CException
     */
     private function loadDoctors() {         
        if (is_null($this->doctors)) {
            $models = $this->doctorSearch->search();
           
            if (arrayNotEmpty($models)) {
                $this->setDoctors($models,$this->doctorsids);
            }
        }
    }

    private function setDoctors(array $models,$doctorsids) {
    
        foreach($doctorsids as $k=>$v){
            foreach ($models as $model) {
                if($v==$model->getId()){
                        $data = new stdClass();
                        $data->id = $model->getId();
                        $data->name = $model->getName();
                        $data->docName = $data->name;   //@TODO delete. not used by ios.
                        $data->mTitle = $model->getMedicalTitle();
                        $data->hpName = $model->getHospitalName();
                        $data->desc =$this->doctorsdesc[$model->getId()];
                        $data->imageUrl = $model->getAbsUrlAvatar(); //@TODO delete. not used by ios.
                       // $data->isContracted = $model->getIsContracted();
                        $this->doctors[] = $data;
                }
            }
        }
        $alive=43200;
        Yii::app()->cache->set($this->key, $this->doctors ,$alive);
    }
    /*生成描述信息数组和医生信息
     * @params $doctorlist 配置文件中doctorlist数组
     * @return $doctorsids 医生信息ID
    */
    protected function set_docotrsid($doctorlist){
        foreach($doctorlist as $key=>$value){
            $this->doctorsids[]=$value['id'];
            $this->doctorsdesc[$value['id']]=$value['desc'];
        }
        return $this->doctorsids;
    }
}
