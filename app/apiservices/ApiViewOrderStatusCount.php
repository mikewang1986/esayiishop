<?php

class ApiViewOrderStatusCount extends EApiViewService {
    private $user;

    // 初始化类的时候将参数注入
    public function __construct($user)
    {
        parent::__construct();
        $this->results = new stdClass();
        $this->user = $user;
    }
    protected function loadData() {
        $this->loadUnreadCount();
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

    private function loadUnreadCount() {
        $booking = new Booking();
        $bookingModels = $booking->getBookingByMobileORUserId($this->user->id, $this->user->username);
        if($bookingModels){
            $this->setUnreadCount($bookingModels);
        }
    }

    private function setUnreadCount(array $bookingModels) {
        foreach ($bookingModels as $model) {
            $data = new stdClass();
            if(in_array($model->bk_status, array(1, 2, 5, 6))){
                $data->num = $model->num;
                $data->bkStatus = $model->bk_status;
                $data->bkStatusText = $model->getBkStatus();
                $this->results->unread[] = $data;
            }
        }
    }




}
