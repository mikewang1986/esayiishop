<?php
namespace app\apiservices;
use app\models\booking\Booking;
use app\models\sales\SalesOrder;
use app\components\StatCode;
use yii;
class ApiViewSalesOrder extends EApiViewService {
    private $refNo;
    private $salesOrder;
    private $bkId;
    private $bkType;
    private $booking;
    private $booking_service_id = null;
    //private $results;
    //初始化类的时候将参数注入
    public function __construct($refNo) {
        parent::__construct();
        $this->refNo = $refNo;
        $this->results = new \stdClass();

    }

    protected function loadData() {

        // load PatientBooking by creatorId.
        $this->loadSalesOrder();
        $this->loadBooking();
    }

    //返回的参数
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

    private function loadSalesOrder() {
        $saleordermodel=new SalesOrder;

        $model = $saleordermodel->getByRefNo($this->refNo);

        if (isset($model)) {
            $bookmodel=new Booking();
            $bookingModel = $bookmodel->getByRefNo($model->bk_ref_no);
            if(isset($bookingModel)){
                if($bookingModel->booking_service_id){
                    $this->booking_service_id = $bookingModel->booking_service_id;
                }
            }
            $this->setSalesOrder($model);
        }
    }

    private function setSalesOrder(SalesOrder $model) {
        $data = new \stdClass();
        $data->id = $model->getId();
        $data->refNo = $model->ref_no;
        $data->bkRefNo = $model->bk_ref_no;
        $data->userId = $model->user_id;
        $data->subject = $model->getSubject();
        $data->description = $model->getDescription();
        $data->finalAmount = $model->getFinalAmount();
        $data->isPaid = $model->getIsPaid(false);
        $data->orderType = $model->getOrderType();
        $data->orderTypeCode = $model->getOrderTypeCode();
        $data->booking_service_id = $this->booking_service_id;
        //判断值
        $this->bkId = $model->getBkId();
        $this->bkType = $model->getBkType();
        $this->salesOrder = $data;
        $this->results->salesOrder = $this->salesOrder;
    }

    //加载booking数据
    private function loadBooking() {
        $model = null;

        if ($this->bkType === StatCode::TRANS_TYPE_BK) {
            $bookmodel=new Booking();
            $model = $bookmodel->getById($this->bkId);
        } elseif ($this->bkType === StatCode::TRANS_TYPE_PB) {
            $bookmodel=new PatientBooking();
            $model = PatientBooking::model()->getById($this->bkId, array('pbDoctor', 'pbPatient'));
        }
        $this->setBooking($model);
    }

    private function setBooking($model) {
        $data = new \stdClass();
        if ($model instanceof Booking) {
            $data->patientName = $model->getContactName();
            $data->mobile = $model->getMobile();
           $data->expertBooked = $model->getExpertBooked();
            $data->diseaseName = $model->getDiseaseName();
           $data->diseaesDetail = $model->getDiseaseDetail();
        } elseif ($model instanceof PatientBooking) {
            $data->expertBooked = ''; // join patient_booking.doctor_id;
            $data->mobile = '';
            $data->patientName = '';
            $doctor = $model->getDoctor();
            if (isset($doctor)) {
                $data->expertBooked = $doctor->getFullname();
                $data->mobile = $doctor->getMobile();
            }
            $patient = $model->getPatient();
            if (isset($patient)) {
                $data->patientName = $patient->getName();
            }
            $data->diseaseName = '';
            $data->diseaesDetail = $model->getDetail();
        }
        $this->booking = $data;
        $this->results->booking = $this->booking;

    }

}
