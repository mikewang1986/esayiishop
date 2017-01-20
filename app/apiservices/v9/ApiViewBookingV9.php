<?php

class ApiViewBookingV9 extends EApiViewService
{

    private $user;

    private $bookingId;
    private $serviceAmount = 0;
    private $depositAmount = 0;
    private $serviceTotalAmount = 0;
    private $depositTotalAmount = 0;
    
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
        if($model->ref_no){
            $modelo = SalesOrder::model()->getOrderByBkIdAndrefNo($this->bookingId, $model->ref_no);
            if (isset($modelo)) {
                if (is_array($modelo)) {
                    foreach ($modelo as $k => $v) {
                        if ($v['order_type'] == SalesOrder::ORDER_TYPE_DEPOSIT) {} else {
                            if($v['is_paid'] == 1){
                                $this->serviceAmount = $v['final_amount'] + $this->serviceAmount ;
                            }
                            $this->serviceTotalAmount = $v['final_amount']+$this->serviceTotalAmount;
                        }
                        if ($v['order_type'] == SalesOrder::ORDER_TYPE_SERVICE) {} else {
                            if($v['is_paid'] == 1){
                                $this->depositAmount = $v['final_amount'];
                            }
                            $this->depositTotalAmount = $v['final_amount'];
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
        $bookingFiles = $model->getBkFiles();
        if (arrayNotEmpty($bookingFiles)) {
            foreach ($bookingFiles as $bookingFile) {
                $files = new stdClass();
                $files->id = $bookingFile->getId();
                $files->absFileUrl = $bookingFile->getAbsFileUrl();
                $files->absThumbnailUrl = $bookingFile->getAbsThumbnailUrl();
                $data->files[] = $files;
            }
        } else {
            $data->files = array();
        }
        $this->results = $data;
    }

}
