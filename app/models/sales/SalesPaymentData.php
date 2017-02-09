<?php
namespace app\models\sales;
use app\models\EActiveRecord;
use Yii;
use yii\helpers\BaseJson;

/**
 * This is the model class for table "sales_payment_data".
 *
 * @property integer $id
 * @property integer $payment_id
 * @property string $request_url
 * @property string $request_data
 * @property string $date_request
 * @property string $return_data
 * @property string $date_return
 * @property string $notify_data
 * @property string $date_notify
 * @property string $vendor_trade_no
 * @property string $vendor_trade_status
 * @property string $error_code
 * @property string $error_msg
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class SalesPaymentData extends EActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sales_payment_data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payment_id'], 'integer'],
            [['date_request', 'date_return', 'date_notify', 'date_created', 'date_updated', 'date_deleted'], 'safe'],
            [['request_url', 'error_msg'], 'string', 'max' => 1024],
            [['request_data', 'return_data', 'notify_data'], 'string', 'max' => 4096],
            [['vendor_trade_no'], 'string', 'max' => 64],
            [['vendor_trade_status', 'error_code'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'payment_id' => 'Payment ID',
            'request_url' => 'Request Url',
            'request_data' => 'Request Data',
            'date_request' => 'Date Request',
            'return_data' => 'Return Data',
            'date_return' => 'Date Return',
            'notify_data' => 'Notify Data',
            'date_notify' => 'Date Notify',
            'vendor_trade_no' => 'Vendor Trade No',
            'vendor_trade_status' => 'Vendor Trade Status',
            'error_code' => 'Error Code',
            'error_msg' => 'Error Msg',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        ];
    }
    public function initFromPayment(SalesPayment $payment, $requestData) {
        $this->payment_id = $payment->id;
        $this->date_request = new \yii\db\Expression('NOW()');
        $this->request_data = BaseJson::encode($requestData);


    }

    /** getters and setters * */
    public function setRequestData($v) {
        $this->request_data = $v;
    }

    public function setReturnData($v) {
        $this->return_data = $v;
    }

    public function setDateReturn($v) {
        $this->date_return = $v;
    }

    public function setNotifyData($v) {
        $this->notify_data = $v;
    }

    public function setDateNotify($v) {
        $this->date_notify = $v;
    }

    public function setErrorCode($v) {
        $this->error_code = $v;
    }

    public function setErrorMsg($v) {
        $this->error_msg = $v;
    }

}
