<?php
namespace app\apiservices;
use app\models\booking\Booking;
use Yii;
use app\models\sales\SalesOrder;
class ApiViewBookingListV5 extends EApiViewService {
    private $user;
    private $bk_status;
    private $isWap;
    //初始化类的时候将参数注入
    public function __construct($user,$bk_status, $isWap=false) {
        parent::__construct();
        $this->results = new \stdClass();
        $this->user = $user;
        $this->bk_status = $bk_status;
        $this->isWap = $isWap;
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
        $bookings=new Booking;
        $models =$bookings->getAllByUserIdOrMobile($this->user->getId(), $this->user->getMobile(), null, array('order' => 'bk_status asc,id DESC'),$this->bk_status, null, $this->isWap);
            // $models = Booking::model()->getAllByUserIdOrMobile($this->user->getId(), $this->user->getMobile(), null, array('order' => 'bk_status asc,id DESC'),$this->bk_status, null, $this->isWap);
        $this->setBookings($models);
    }

    private function setBookings($models) {
        if (arrayNotEmpty($models)) {
            foreach ($models as $model) {
                $data = new \stdClass();
                $data->id = $model->getId();
                $data->userId = $model->getUserId();
                $data->refNo = $model->getrefNo();
                $data->bkStatus = $model->getBkStatusCode();
                $data->bkStatusText = $model->getBkStatus();
                $data->patientName = $model->getContactName();//患者姓名
                $data->doctorName = $model->getDoctorName();
                $data->mobile = $model->mobile;
                $data->expertName = $model->getExpertNameBooked();//医生
               $data->hpName = $model->gethospitalName();//医院
                $data->hpDeptName = $model->gethpDeptName();//科室
                $data->dateStart = $model->getDateStart();
                $data->dateEnd = $model->getDateEnd(); 
                $data->actionUrl = Yii::$app->urlManager->createAbsoluteUrl('/api/userbooking/' . $data->id);
                //add by wanglei 
                $data->booking_service_id = $model->booking_service_id;
                $SalesOrders=new SalesOrder;
                $salemodel = $SalesOrders->getByBkRefNo($data->refNo);

                if(isset($salemodel)){
                   $serviceAmount=0;
                   $serviceTotalAmount=0;
                   $depositAmount=0;
                   $depositTotalAmount=0;
                   $sale_orderarray=array();
                   $deposit_array=array();
                   foreach($salemodel as $sale){
                        $data->order_type=$sale->order_type; 
                        $data->is_paid=$sale->getIsPaid(false);//是否支付
                        $data->order_typename=$sale->getOrderType(true);//预约金还是服务费
                        if ($data->order_type == SalesOrder::ORDER_TYPE_DEPOSIT) {
                            if( $data->is_paid == 1){
                                $depositAmount = $sale->getFinalAmount();
                            } 
                            $depositTotalAmount = $sale->getFinalAmount();
                            $depositarray=new \stdClass();
                            $depositarray->id=$sale->id;
                            $depositarray->user_id=$sale->user_id;
                            $depositarray->final_amount=$sale->final_amount;
                            $depositarray->total_amount=$sale->total_amount;
                            $depositarray->ref_no=$sale->ref_no;
                            $depositarray->bk_ref_no=$sale->bk_ref_no;
                            $depositarray->is_paid=$sale->is_paid;
                            $depositarray->date_created=$sale->date_created;
                            //支付时间
                            $depositarray->date_closed=$sale->date_closed;
                            $deposit_array[]=$depositarray;
                        }
                        if ($data->order_type == SalesOrder::ORDER_TYPE_SERVICE) {
                            if($data->is_paid == 1){
                                $serviceAmount = $sale->getFinalAmount()+$serviceAmount;
                            }
                            $serviceTotalAmount = $sale->getFinalAmount()+$serviceTotalAmount;
                            $salelist=new \stdClass();
                            $salelist->id=$sale->id;
                            $salelist->user_id=$sale->user_id;
                            $salelist->final_amount=$sale->final_amount;
                            $salelist->total_amount=$sale->total_amount;
                            $salelist->ref_no=$sale->ref_no;
                            $salelist->bk_ref_no=$sale->bk_ref_no;
                            $salelist->is_paid=$sale->is_paid;
                            $salelist->date_created=$sale->date_created;
                            //支付时间
                            $salelist->date_closed=$sale->date_closed;
                            $sale_orderarray[]=$salelist;
                        }
                         //疾病名称
                        $data->disease_name=$sale->subject;
                         //疾病描述
                        $data->disease_detail=$sale->getDescription();
                        
                        }
                $data->serviceAmount = $serviceAmount;
                $data->depositAmount = $depositAmount;
                $data->serviceTotalAmount = $serviceTotalAmount;
                $data->depositTotalAmount = $depositTotalAmount;
                $data->saleList=$sale_orderarray;
                $data->depositList=$deposit_array;
                $this->results->booking[] = $data;
            }
        }
        }else {
            $this->results->booking = array();
        }
    }
  

}
