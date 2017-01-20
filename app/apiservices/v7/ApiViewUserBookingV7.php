<?php

class ApiViewUserBookingV7 extends EApiViewService {

    private $id;
    private $bk_id;
    private $booking;
    private $salesOrder;

    public function __construct($id) {
        parent::__construct();
        $this->id = $id;
        $this->booking = null;
        $this->salesOrder = array();
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

    protected function loadData() {
        $this->loadBooking();
        $this->loadSalesOrder();
    }

    private function loadBooking() {
        $model = Booking::model()->getById($this->id);
        if (isset($model)) {
            $this->setBooking($model);
        }
        $this->results->booking = $this->booking;
    }

    private function setBooking(Booking $model) {
        $data = new stdClass();
        $data->id = $model->getId();
        $data->refNo = $model->getRefNo();
        $this->bk_id = $model->getId();
        $data->expertName = $model->getExpertNameBooked();
        $data->patientName = $model->getContactName();
        $data->mobile = $model->getMobile();
        $data->hpName = $model->gethospitalName();
        $data->hpDeptName = $model->gethpDeptName();
        $data->dateStart = $model->getDateStart('m月d日');
        $data->dateEnd = $model->getDateEnd('m月d日');
        $data->statusText = $model->getStatusText();
        $data->status = $model->bk_status;
        $data->diseaseName = $model->getDiseaseName();
        $data->diseaseDetail = $model->getDiseaseDetail();
        $data->dateUpdate = $model->getDateUpdated();
        $data->isDepositPaid = $model->is_deposit_paid;
        
        $this->booking = $data;
    }

    private function loadSalesOrder() {
        if (strIsEmpty($this->bk_id) === false) {
            $bkType = StatCode::TRANS_TYPE_BK;
            $models = SalesOrder::model()->getByBkIdAndBkType($this->bk_id, $bkType, '*', null, null);
            if (arrayNotEmpty($models)) {
                $this->setSalesOrder($models);
            }
        }
        $this->results->salesOrder = $this->salesOrder;
    }

    private function setSalesOrder($models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->refNo = $model->ref_no;
            $data->userId = $model->user_id;
            $data->subject = $model->getSubject();
            $data->description = $model->getDescription();
            $data->finalAmount = $model->getFinalAmount();
            $data->isPaid = $model->getIsPaid(false);
            $data->orderType = $model->getOrderType();
            $this->salesOrder[] = $data;
        }
    }

}
