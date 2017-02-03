<?php
namespace app\models\sales;
use app\models\EActiveRecord;
use Yii;
/**
 * This is the model class for table "sales_order".
 *
 * @property integer $id
 * @property string $ref_no
 * @property integer $user_id
 * @property string $bk_ref_no
 * @property integer $bk_id
 * @property integer $bk_type
 * @property integer $admin_booking_id
 * @property string $crm_no
 * @property string $subject
 * @property string $description
 * @property string $ping_id
 * @property string $order_type
 * @property integer $is_unsystem_pay
 * @property integer $is_paid
 * @property string $date_open
 * @property string $date_closed
 * @property string $created_by
 * @property string $total_amount
 * @property integer $discount_percent
 * @property string $discount_amount
 * @property string $final_amount
 * @property string $currency
 * @property string $bd_code
 * @property string $cash_back
 * @property string $patient_name
 * @property string $patient_mobile
 * @property string $patient_age
 * @property string $patient_identity
 * @property string $patient_state
 * @property string $patient_city
 * @property string $patient_adress
 * @property string $disease_name
 * @property string $disease_detail
 * @property string $expected_doctor_name
 * @property string $expected_hospital_name
 * @property string $expected_hp_dept_name
 * @property string $creator_doctor_name
 * @property string $creator_hospital_name
 * @property string $creator_dept_name
 * @property string $final_doctor_name
 * @property string $final_doctor_hospital
 * @property string $final_time
 * @property string $customer_request
 * @property string $customer_intention
 * @property string $customer_type
 * @property string $customer_diversion
 * @property string $customer_agent
 * @property string $travel_type
 * @property string $admin_user_name
 * @property string $date_invalid
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * @property AdminBooking $adminBooking
 * @property SalesPayment[] $salesPayments
 */
class SalesOrder  extends EActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sales_order';
    }
    //表示预约定金
    const ORDER_TYPE_DEPOSIT = 'deposit';   // 预约金
    const ORDER_TYPE_SERVICE = 'service';   // 服务费
    const ORDER_AMOUNT_DEPOSIT = 1000;
    const ORDER_AMOUNT_SERVICE = 1000;
    const ORDER_UNPAIDED = 0;
    const ORDER_PAIDED = 1;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ref_no'], 'required'],
            [['user_id', 'bk_id', 'bk_type', 'admin_booking_id', 'is_unsystem_pay', 'is_paid', 'discount_percent'], 'integer'],
            [['date_open', 'date_closed', 'date_invalid', 'date_created', 'date_updated', 'date_deleted'], 'safe'],
            [['total_amount', 'discount_amount', 'final_amount'], 'number'],
            [['ref_no'], 'string', 'max' => 16],
            [['bk_ref_no', 'order_type'], 'string', 'max' => 20],
            [['crm_no', 'created_by', 'bd_code', 'cash_back', 'patient_name', 'patient_mobile', 'patient_age', 'patient_identity', 'patient_state', 'patient_city', 'expected_doctor_name', 'expected_hospital_name', 'expected_hp_dept_name', 'creator_doctor_name', 'creator_hospital_name', 'creator_dept_name', 'final_doctor_name', 'final_doctor_hospital', 'final_time', 'customer_request', 'customer_intention', 'customer_type', 'customer_diversion', 'customer_agent', 'travel_type', 'admin_user_name'], 'string', 'max' => 50],
            [['subject', 'disease_name'], 'string', 'max' => 100],
            [['description', 'disease_detail'], 'string', 'max' => 1000],
            [['ping_id'], 'string', 'max' => 30],
            [['currency'], 'string', 'max' => 3],
            [['patient_adress'], 'string', 'max' => 200],
            [['ref_no'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ref_no' => '订单号',
            'user_id' => 'User ID',
            'bk_ref_no' => '预约号',
            'bk_id' => '预约编号',
            'bk_type' => '预约类型',
           // 'admin_booking_id' => 'Admin Booking ID',
            'crm_no' => 'CRM单号',
            'subject' => '订单标题',
            'description' => '订单详情',
            'ping_id' => 'ping++付款ID',
           // 'order_type' => 'Order Type',
           // 'is_unsystem_pay' => 'Is Unsystem Pay',
            'is_paid' => '支付情况',
            'date_open' => '订单创建时间',
            'date_closed' => '订单关闭时间',
            'created_by' => 'Created By',
            'total_amount' => '金额',
            'discount_percent' => 'Discount Percent',
            'discount_amount' => 'Discount Amount',
            'final_amount' => '金额',
            'currency' => '货币',
            'bd_code' => '地推',
           // 'cash_back' => 'Cash Back',
          //  'patient_name' => 'Patient Name',
         //   'patient_mobile' => 'Patient Mobile',
          /*  'patient_age' => 'Patient Age',
            'patient_identity' => 'Patient Identity',
            'patient_state' => 'Patient State',
            'patient_city' => 'Patient City',
            'patient_adress' => 'Patient Adress',
            'disease_name' => 'Disease Name',
            'disease_detail' => 'Disease Detail',
            'expected_doctor_name' => 'Expected Doctor Name',
            'expected_hospital_name' => 'Expected Hospital Name',
            'expected_hp_dept_name' => 'Expected Hp Dept Name',
            'creator_doctor_name' => 'Creator Doctor Name',
            'creator_hospital_name' => 'Creator Hospital Name',
            'creator_dept_name' => 'Creator Dept Name',
            'final_doctor_name' => 'Final Doctor Name',
            'final_doctor_hospital' => 'Final Doctor Hospital',
            'final_time' => 'Final Time',
            'customer_request' => 'Customer Request',
            'customer_intention' => 'Customer Intention',
            'customer_type' => 'Customer Type',
            'customer_diversion' => 'Customer Diversion',
            'customer_agent' => 'Customer Agent',
            'travel_type' => 'Travel Type',
            'admin_user_name' => 'Admin User Name',
            'date_invalid' => 'Date Invalid',*/
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdminBookings()
    {
        return $this->hasOne(AdminBooking::className(), ['id' => 'admin_booking_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalesPayments()
    {
        return $this->hasMany(SalesPayment::className(), ['order_id' => 'id']);
    }
    //查看预约单的支付情况
    public function getByBkIdAndBkTypeAndOrderType($bkId, $bkType, $orderType, $attributes, $with, $options) {
        return $this->getByAttributes(array('bk_id' => $bkId, 'bk_type' => $bkType, 'order_type' => $orderType), $with);
    }

    //查看预约单的所有支付情况
    public function getByBkIdAndBkType($bkId, $bkType, $attributes, $with, $options) {
        return $this->getAllByAttributes(array('bk_id' => $bkId, 'bk_type' => $bkType), $with);
    }

    //根据预约号查询支付情况
    public function getByRefNo($refNo) {
        return $this->getByAttributes(array('ref_no' => $refNo));
    }

    public function getByBkRefNo($bkRefNo) {
        return $this->getAllByAttributes(array('bk_ref_no' => $bkRefNo),null,null,"app\models\sales\SalesOrder");
    }

    public function initFromBk($model) {
        $this->ref_no = $model->ref_no;
        $this->user_id = $model->user_id;
        $this->bk_id = $model->id;
        $this->bk_type = StatCode::TRANS_TYPE_BK;
        $this->is_paid = 0;
        $this->created_by = Yii::$app->user->id;
        $this->date_open = new DateTime();
    }

    //来自PatientBooking数据
    public function initSalesOrder($model) {
        $this->createRefNo($model->refNo, $model->id, $model->bk_type);
        $this->user_id = $model->user_id;
        $this->bk_id = $model->id;
        $this->bk_type = $model->bk_type;
        $this->is_paid = 0;
        $this->order_type = SalesOrder::ORDER_TYPE_DEPOSIT;
        $this->subject = $model->subject;
        $this->description = $model->description;
        $this->created_by = Yii::$app->user->id;
        $this->date_open = date('Y-m-d H:i:s');
        $this->setAmount($model->amount);
    }

    public function createRefNo($refNo, $bkId, $bkType) {
        $db = Yii::$app->db;
        $sql = "SELECT COUNT(*) FROM sales_order WHERE bk_id = " . $bkId . " AND bk_type = " . $bkType . " AND date_deleted IS NULL";
        $result = $db->createCommand($sql)->query();
        foreach ($result as $r) {
            $count = $r['COUNT(*)'] + 1;
        }
        if ($count < 10) {
            $count = '0' . $count;
        }
        $this->ref_no = $refNo . $count;
    }

    public function createRefNo2($bkrefno) {
        $db = Yii::$app->db;
        $sql = "SELECT COUNT(*) FROM sales_order WHERE bk_ref_no = '" . $bkrefno . "' AND date_deleted IS NULL";
        $result = $db->createCommand($sql)->query();
        foreach ($result as $r) {
            $count = $r['COUNT(*)'] + 1;
        }
        if ($count < 10) {
            $count = '0' . $count;
        }
        $this->ref_no = $bkrefno . $count;
    }

    public function setAmount($v) {
        //prepare for auto calculate amount
        //no discount now
        $this->final_amount = $v;
        $this->total_amount = $this->final_amount;
        $this->discount_percent = 0;
        $this->discount_amount = 0;
    }

    /** getters and setters * */
    public function getSubject() {
        return $this->subject;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setSubject($v) {
        $this->subject = $v;
    }

    public function setDescription($v) {
        $this->description = $v;
    }

    public function setIsPaid($v) {
        $this->is_paid = $v;
    }

    public function setDateOpen($v) {
        $this->date_open = $v;
    }

    public function setDateClosed($v) {
        $this->date_closed = $v;
    }

    public function setBkRefNo($v) {
        $this->bk_ref_no = $v;
    }

    public function getIsPaid($v = true) {
        if ($v) {
            return $this->is_paid == 1 ? '已支付' : '待支付';
        } else {
            return $this->is_paid;
        }
    }

    public function getRefNo() {
        return $this->ref_no;
    }

    public function getFinalAmount() {
        return $this->final_amount;
    }

    public function getDateClosed() {
        return $this->date_closed;
    }

    public function getOptionsOrderType() {
        return array(
            self::ORDER_TYPE_DEPOSIT => '预约金',
            self::ORDER_TYPE_SERVICE => '服务费',
        );
    }

    public function getOrderType($text = true) {
        if ($text) {
            $options = self::getOptionsOrderType();
            if (isset($options[$this->order_type])) {
                return $options[$this->order_type];
            } else {
                return '';
            }
        } else {
            return $this->order_type;
        }
    }

    public function getOrderTypeDefaultAmount() {
        if ($this->order_type == self::ORDER_TYPE_DEPOSIT) {
            return self::ORDER_AMOUNT_DEPOSIT;
        } elseif ($this->order_type == self::ORDER_TYPE_SERVICE) {
            return self::ORDER_AMOUNT_SERVICE;
        } else {
            return 0;
        }
    }

    public function getBkId() {
        return $this->bk_id;
    }

    public function getBkType() {
        return $this->bk_type;
    }

    public function setBkId($v) {
        $this->bk_id = $v;
    }

    public function setBkType($v) {
        $this->bk_type = $v;
    }

    public function getBdCode() {
        return $this->bd_code;
    }

    public function setBdCode($v) {
        $this->bd_code = $v;
    }

    public function getPingId() {
        return $this->ping_id;
    }

    public function setPingId($v) {
        $this->ping_id = $v;
    }

    public function getOrderTypeCode() {
        return $this->order_type;
    }


    public function getDateClose($format = self::DB_FORMAT_DATETIME) {
        $date = new DateTime($this->date_closed);
        if ($date === false) {
            return null;
        } else
            return $date->format($format);
    }

    private function setBookingSalesOrders(SalesOrder $model){
        if (arrayNotEmpty($models)) {
            foreach ($models as $model) {
                $data = new stdClass();
                $data->id = $model->getId();
                $data->refNo = $model->getrefNo();
                $data->bkStatus = $model->getBkStatusNum();
                $data->bkStatusText = $model->getBkStatus();
                $data->contact_name = $model->getContactName();
                $data->expertName = $model->getExpertNameBooked();
                $data->hpName = $model->gethospitalName();
                $data->hpDeptName = $model->gethpDeptName();
                $data->dateStart = $model->getDateStart();
                $data->dateEnd = $model->getDateEnd();
                $data->actionUrl = Yii::$app->urlManager->createAbsoluteUrl('/api/userbooking/' . $data->id);
                $this->results->booking[] = $data;
            }
        } else {
            $this->results->booking = array();
        }
    }

    public function getOrderByBkIdAndrefNo($BkId,$refNo){
        return $this->getAllByAttributes(array('bk_id' => $BkId, 'bk_ref_no' => $refNo));

    }
}
