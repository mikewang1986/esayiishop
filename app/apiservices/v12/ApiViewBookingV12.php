<?php

class ApiViewBookingV12 extends EApiViewService
{

    private $user;
    private $bookingId;
    private $serviceAmount = 0;
    private $depositAmount = 0;
    private $serviceTotalAmount = 0;
    private $depositTotalAmount = 0;
    private $saleRefNo;
    private $saleList;
    //服务费区分
    private $depositList;
    
    // 初始化类的时候将参数注入
    public function __construct($user, $id)
    {
        parent::__construct();
        $this->results = new stdClass();
        $this->user = $user;
        $this->bookingId = $id;
    }

    protected function loadData()
    {
        // load Booking by id.
        $this->loadBooking();
    }
    
    // 返回的参数
    protected function createOutput()
    {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->results
            );
        }
    }

    private function loadBooking()
    {
        // $model = Booking::model()->getByIdAndUserId($this->bookingId, $this->user->getId());
        // 旧的booking.user_id 为NULL， 所以在查找时，需要比较 (user_id=$userId OR mobile=$mobile);
        $model = Booking::model()->getByIdAndUser($this->bookingId, $this->user->getId(), $this->user->getMobile());
        //***doctor_id找科室
        if($model->ref_no){
            $modelo = SalesOrder::model()->getOrderByBkIdAndrefNo($this->bookingId, $model->ref_no);
            if (isset($modelo)) {
                if (is_array($modelo)) {
                    $hk=0;
                    $sk=0;
                    foreach ($modelo as $k => $v) {
                        if ($v['order_type'] == SalesOrder::ORDER_TYPE_DEPOSIT) {
                            $this->saleRefNo = $v['ref_no'];
                            //加入服务费拆分
                            $this->depositList[$hk]['id'] = $v->id;
                            $this->depositList[$hk]['ref_no'] = $v->ref_no;
                            $this->depositList[$hk]['user_id'] = $v->user_id;
                            $this->depositList[$hk]['bk_id'] = $v->bk_id;
                            $this->depositList[$hk]['bk_ref_no'] = $v->bk_ref_no;
                            $this->depositList[$hk]['is_paid'] = $v->is_paid;
                            $this->depositList[$hk]['total_amount'] = $v->total_amount;
                            $this->depositList[$hk]['final_amount'] = $v->final_amount;
                            $this->depositList[$hk]['date_created'] = $v->date_created;
                            //支付完成时间
                            $this->depositList[$hk]['date_closed'] = $v->date_closed;
                            if($v['is_paid'] == 1){
                                $this->depositAmount = $v['final_amount'];
                            }
                            $this->depositTotalAmount = $v['final_amount'];
                            $hk++;
                        }
                        if ($v['order_type'] == SalesOrder::ORDER_TYPE_SERVICE) {
                            //加入服务费拆分
                            $this->saleList[$sk]['id'] = $v->id;
                            $this->saleList[$sk]['ref_no'] = $v->ref_no;
                            $this->saleList[$sk]['user_id'] = $v->user_id;
                            $this->saleList[$sk]['bk_id'] = $v->bk_id;
                            $this->saleList[$sk]['bk_ref_no'] = $v->bk_ref_no;
                            $this->saleList[$sk]['is_paid'] = $v->is_paid;
                            $this->saleList[$sk]['total_amount'] = $v->total_amount;
                            $this->saleList[$sk]['final_amount'] = $v->final_amount;
                            $this->saleList[$sk]['date_created'] = $v->date_created;
                            //支付完成时间
                            $this->saleList[$sk]['date_closed'] = $v->date_closed;
                            if($v['is_paid'] == 1){
                                $this->serviceAmount = $v['final_amount'] + $this->serviceAmount ;
                            }
                            $this->serviceTotalAmount = $v['final_amount']+$this->serviceTotalAmount;
                            $sk++;
                        }
            
                    }
                }
            }
        }
        if (isset($model)) {
            $this->setBooking($model);
        }
        
        
    }

    private function setBooking(Booking $model)
    {
        $data = new stdClass();
        $data->id = $model->getId();
        $data->refNo = $model->getRefNo();
        $data->saleRefNo = $this->saleRefNo;
        $data->userId = $model->getUserId();
        $data->bkStatus = $model->getBkStatusCode();
        $data->expertName = $model->getExpertNameBooked();
        $data->mobile = $model->getMobile();
        $data->hospitalName = $model->getHospitalName();
        $data->hpDeptName = $model->getHpDeptName();
        $data->patientName = $model->getContactName();
        $data->diseaseName = $model->getDiseaseName();
        $data->diseaseDetail = $model->getDiseaseDetail(false); // 不要自动添加<br>.
        $data->dateCreated = $model->getDateCreated();
        $data->dateStart = $model->getDateStart();
        $data->dateEnd = $model->getDateEnd();
        $data->serviceAmount = $this->serviceAmount;
        $data->depositAmount = $this->depositAmount;
        $data->serviceTotalAmount = $this->serviceTotalAmount;
        $data->depositTotalAmount = $this->depositTotalAmount;
        $data->actionUrl = Yii::app()->createAbsoluteUrl('/api2/bookingfile');
        if(!empty($model->date_updated)){
            $data->dateUpdated= $model->getDateUpdated('Y-m-d H:i:s');
        }
        $bookingFiles = $model->getBkFiles();
        if (arrayNotEmpty($bookingFiles)) {
            foreach ($bookingFiles as $bookingFile) {  
               if($bookingFile->date_deleted==null){
                $files = new stdClass();
                $files->id = $bookingFile->getId();
                $files->absFileUrl = $bookingFile->getAbsFileUrl();
                $files->absThumbnailUrl = $bookingFile->getAbsThumbnailUrl();
                $files->remote_domain = $bookingFile->remote_domain;
                $files->remote_file_key = $bookingFile->remote_file_key;
                $data->files[] = $files;
               }
            }
        } else {
            $data->files = array();
        }
        if ($data->bkStatus == StatCode::BK_STATUS_DONE) {//已完成
            $evaluation = new Evaluation();
            $bookingEvaluation = $evaluation->getBookingIds($data->id);
            if($bookingEvaluation){
                $std = new stdClass();
                $std->customerServiceEvaluation = $bookingEvaluation->customer_service_evaluation;
                $std->platformEvaluation = $bookingEvaluation->platform_evaluation;
                $std->medicalEvaluation = $bookingEvaluation->medical_evaluation;
                $std->suggestedRemarks = $bookingEvaluation->suggested_remarks;
                $data->evaluation = $std;
            }
        }
        if(isset($this->saleList)){
            $data->saleList = $this->saleList;
        }else{
            $data->saleList = array();
        }
        //add by wanglei
        if(isset($this->depositList)){
            $data->depositList = $this->depositList;
        }else{
            $data->depositList = array();
        }
        $this->results = $data;
        
    }

}
