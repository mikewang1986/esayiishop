<?php
class ApiViewEvaluationListV12 extends EApiViewService{
    private $user_id;
    private $evaluation_id; 
    //初始化类的时候将参数注入
    public function __construct($user,$id) {
        parent::__construct();
        $this->results = new stdClass();
        $this->user_id = $user;
        $this->evaluation_id = $id;
    }
    protected function loadData() {
        // load PatientBooking by creatorId.
        $this->loadEvaluation();        
    }
    //返回的参数
    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->results->evaluation,
            );
        }
    }
    
    //加载booking的数据
    private function loadEvaluation(){
        $evaluation = new Evaluation();
        $bookingEvaluation = $evaluation->getInfobyuserid($this->evaluation_id,$this->user_id);
        $std = new stdClass();
        if($bookingEvaluation){
            $std->customerServiceEvaluation = $bookingEvaluation->customer_service_evaluation;
            $std->platformEvaluation = $bookingEvaluation->platform_evaluation;
            $std->medicalEvaluation = $bookingEvaluation->medical_evaluation;
            $std->suggestedRemarks = $bookingEvaluation->suggested_remarks;
        }
        $this->results->evaluation=$std;
    }
    
   
}
