<?php

class ApiViewBookingListVendor extends EApiViewService {

    private $user;
    private $bk_status;
    private $vendorId;
    //初始化类的时候将参数注入
    public function __construct($user,$bk_status,$vendorId=0) {
        parent::__construct();
        $this->results = new stdClass();
        $this->user = $user;
        $this->bk_status = $bk_status;
        $this->vendorId = $vendorId;
//        $this->bookingMgr = new BookingManager();
//        $this->Bookings=array();
    }

    protected function loadData() {
        // load PatientBooking by creatorId.
        $this->loadBookings();
    }

    //返回的参数
    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->results->booking,
            );
        }
    }

    //加载booking的数据
    private function loadBookings() {
        if($this->user){
            $models = Booking::model()->getAllByUserIdOrMobile($this->user->getId(), $this->user->getMobile(), null, array('order' => 'id DESC'),$this->bk_status,$this->vendorId);
            $this->setBookings($models);
        }else{
            $this->results->booking = array();
        }

    }

    private function setBookings($models) {
        if (arrayNotEmpty($models)) {
            foreach ($models as $model) {
                $data = new stdClass();
                $data->id = $model->getId();
                $data->refNo = $model->getrefNo();
                $data->bkStatus = $model->getBkStatus(false);
                $data->bkStatusText = $model->getBkStatus();
                $data->contact_name = $model->getContactName();
                $data->expertName = $model->getExpertNameBooked();
                $data->hpName = $model->gethospitalName();
                $data->hpDeptName = $model->gethpDeptName();
                $data->dateStart = $model->getDateStart();
                $data->dateEnd = $model->getDateEnd();
                $data->actionUrl = Yii::app()->createAbsoluteUrl('/api/userbooking/' . $data->id);
                $this->results->booking[] = $data;
            }
        } else {
            $this->results->booking = array();
        }
    }

}
