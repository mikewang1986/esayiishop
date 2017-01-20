<?php

class ApiViewOrderList extends EApiViewService {
    private $user;
    private $bookingId;

    // 初始化类的时候将参数注入
    public function __construct($user, $id)
    {
        parent::__construct();
        $this->results = new stdClass();
        $this->user = $user;
        $this->bookingId = $id;
    }
    protected function loadData() {
        $this->loadOrderList();
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

    private function loadOrderList() {
        $booking = Booking::model()->getById($this->bookingId);
        if ($booking) {
            $payList = SalesOrder::model()->getOrderByBkIdAndrefNo($this->bookingId, $booking->ref_no);
            if($payList){
                $this->setOrderList($payList);
            }
        }

    }

    private function setOrderList(array $payList) {
        $hasPay = 0;
        $noPay = 0;
        foreach ($payList as $pay) {
            if($pay->order_type == 'service'){
                $data = new stdClass();
                $data->id = $pay->id;
                $data->saleRefNo = $pay->ref_no;
                $data->orderType = $pay->order_type;
                $data->finalAmount = $pay->final_amount;
                $data->isPaid = $pay->is_paid;
                if($data->isPaid == 1){
                    $hasPay += $pay->final_amount;
                }else{
                    $noPay += $pay->final_amount;
                }
                $this->results->orders[] = $data;
            }
        }
        $this->results->hasPay = $hasPay;
        $this->results->noPay = $noPay;
    }

}
