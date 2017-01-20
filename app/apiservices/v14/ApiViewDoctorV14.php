<?php

class ApiViewDoctorV14 extends EApiViewService {

    private $doctor_id;
    private $members;
    private $subCatId;
    private $comment;
    private $isServiceId = null;
    
    
    public function __construct($id) {
        parent::__construct();
        $this->doctor_id = $id;
    }

    protected function loadData() {
        $this->loadDoctor();
//        $this->loadRelatedDoctors();
        $this->loadDoctorsComment();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                "errorMsg" => "success",
                'results' => $this->results,
            );
        }
    }
    private function loadDoctor(){
        $doctor = Doctor::model()->getById($this->doctor_id);
        //义诊医生判断：1常规2义诊  默认值null
        $bookingServiceJoin = BookingServiceDoctorJoin::model()->getByDoctorIdAndBookingServiceId($this->doctor_id, BookingServiceConfig::BOOKING_SERVICE_FREE_LIINIC);
        if (isset($bookingServiceJoin)) {
            $this->isServiceId = BookingServiceConfig::BOOKING_SERVICE_FREE_LIINIC;
        }
        $this->setDoctor($doctor);
        if(isset($this->members)){
            $this->setMembers();
        }
    }

    private function setMembers() {
        array_shift($this->members);
        foreach($this->members as $model){
            $data = new stdClass();
            $data->id = $model->getId();
            $data->name = $model->getName();
            $data->hospitalId = $model->getHospitalId();
            $data->hospitalName = $model->getHospitalName();
            $data->mTitle = $model->getMedicalTitle();
            $data->aTitle = $model->getAcademicTitle();
            $data->imageUrl = $model->getAbsUrlAvatar();
            $data->hpDeptId = $model->getHpDeptId();
            $data->hpDeptName = $model->getHpDeptName();
            $data->isExpteam = $model->getIsExpteam();
            $data->description = $model->getDescription();
            $data->careerExp = $model->getCareerExp();
            $data->honour = $model->getHonourList();
            $this->results->members[] = $data;
        }
    }

    private function setDoctor(Doctor $model) {
        $data = new stdClass();
        $data->id = $model->getId();
        $data->name = $model->getName();
        $data->hospitalId = $model->getHospitalId();
        $data->hospitalName = $model->getHospitalName();
        $data->mTitle = $model->getMedicalTitle();
        $data->aTitle = $model->getAcademicTitle();
        $data->imageUrl = $model->getAbsUrlAvatar();
        $data->hpDeptId = $model->getHpDeptId();
        $data->hpDeptName = $model->getHpDeptName();
        $data->isExpteam = $model->getIsExpteam();
        $data->description = $model->getDescription();
        $data->careerExp = $model->getCareerExp();
        $data->isServiceId = $this->isServiceId;
        $data->honour = $model->getHonourList();
        $data->reasons = $model->getReasons();
        $data->isContracted = $model->getIsContracted();
        if($data->isExpteam){
            $this->members = ExpertTeam::model()->getById($model->getExpteamId())->getMembers();
        }
        $this->results->doctor = $data;
    }

    private function loadRelatedDoctors(){
        $diseaseDoctorJoin = DiseaseDoctorJoin::model()->getAllByDoctorId($this->doctor_id);
        if(arrayNotEmpty($diseaseDoctorJoin)){
            $diseaseId = $diseaseDoctorJoin[0]->disease_id;
            $categoryDiseaseJoin = CategoryDiseaseJoin::model()->getById($diseaseId);
            if(isset($categoryDiseaseJoin)){
                $this->subCatId = $categoryDiseaseJoin->getSubCatId();
            }
            $this->setNavigation($this->subCatId);
            $doctors = Doctor::model()->getByDiseaseId($diseaseId, $this->doctor_id);
            if(isset($doctors)){
                $this->setRelatedDoctors($doctors);
            }
        }

    }

    public function setRelatedDoctors($doctors){
        foreach($doctors as $doctor){
            $data = new stdClass();
            $data->id = $doctor->getId();
            $data->name = $doctor->getName();
            $data->hospitalId = $doctor->getHospitalId();
            $data->hospitalName = $doctor->getHospitalName();
            $data->mTitle = $doctor->getMedicalTitle();
            $data->aTitle = $doctor->getAcademicTitle();
            $data->imageUrl = $doctor->getAbsUrlAvatar();
            $data->hpDeptId = $doctor->getHpDeptId();
            $data->hpDeptName = $doctor->getHpDeptName();
            $data->isExpteam = $doctor->getIsExpteam();
            $this->results->related[] = $data;
        }
    }

    public function setNavigation($subCatId){
        $model = DiseaseCategory::model()->getBySubCatId($subCatId, 8);
        $data = new stdClass();
        $data->cate_id = $model->getCategoryId();
        $data->cate_name = $model->getCategoryName();
        $data->sub_cate_id = $model->getSubCategoryId();
        $data->getSubCategoryName = $model->getSubCategoryName();
        $this->results->navigation = $data;
    }    

   /**
     * 读取医生评价
     */
    private function loadDoctorsComment(){
        
        if (isset($this->doctor_id)) {
            $comment = Comment::model()->getAllByDoctorId($this->doctor_id);
            if (arrayNotEmpty($comment)) {
                $this->setDoctorComments($comment);
            }
        }
    }
    
    /**
     * 评价表赋值
     */
    public function setDoctorComments($comments){
        foreach($comments as $comment){
            $data = new stdClass();
            $data->id = $comment->getId();
            $data->user_id = $comment->getUseriId();
            $data->user_name = $comment->getUserName();
            $data->bk_type = $comment->getBkType();
            $data->doctor_id = $comment->getDoctorId();
            $data->bk_id = $comment->getBkId();
            $data->effect = $comment->getEffect();
            $data->doctor_attitude = $comment->getDoctorAttitude();
            $data->comment_text = $comment->getCommentText();
            $data->disease_detail = $comment->getDiseaseDetail();
            $this->results->comment[] = $data;
        }
    }
    
}
