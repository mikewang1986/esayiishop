<?php
namespace app\models\sales;
use app\models\EActiveRecord;
use Yii;

/**
 * This is the model class for table "sales_payment".
 *
 * @property integer $id
 * @property string $uid
 * @property integer $order_id
 * @property integer $user_id
 * @property string $ping_charge_id
 * @property string $pay_channel
 * @property string $channel_trade_no
 * @property integer $payment_status
 * @property string $bill_amount
 * @property string $bill_currency
 * @property string $bill_date
 * @property string $paid_amount
 * @property string $paid_date
 * @property string $subject
 * @property string $description
 * @property string $buyer_account
 * @property string $user_host_ip
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class SalesPayment extends EActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sales_payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid'], 'required'],
            [['order_id', 'user_id', 'payment_status'], 'integer'],
            [['bill_amount', 'paid_amount'], 'number'],
            [['bill_date', 'paid_date', 'date_created', 'date_updated', 'date_deleted'], 'safe'],
            [['uid'], 'string', 'max' => 32],
            [['ping_charge_id'], 'string', 'max' => 50],
            [['pay_channel'], 'string', 'max' => 30],
            [['channel_trade_no', 'subject', 'description', 'buyer_account'], 'string', 'max' => 100],
            [['bill_currency'], 'string', 'max' => 3],
            [['user_host_ip'], 'string', 'max' => 15],
            [['uid'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'order_id' => 'Order ID',
            'user_id' => 'User ID',
            'ping_charge_id' => 'Ping Charge ID',
            'pay_channel' => 'Pay Channel',
            'channel_trade_no' => 'Channel Trade No',
            'payment_status' => 'Payment Status',
            'bill_amount' => 'Bill Amount',
            'bill_currency' => 'Bill Currency',
            'bill_date' => 'Bill Date',
            'paid_amount' => 'Paid Amount',
            'paid_date' => 'Paid Date',
            'subject' => 'Subject',
            'description' => 'Description',
            'buyer_account' => 'Buyer Account',
            'user_host_ip' => 'User Host Ip',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        ];
    }
    public function initFromOrder(SalesOrder $order, $channel) {
        $this->order_id = $order->id;
        $this->user_id = $order->user_id;
        $this->uid = strRandom();
        $this->bill_amount = $order->final_amount;
        $this->bill_currency = 'RMB';
        $this->bill_date =new \yii\db\Expression('NOW()');
        $this->payment_status = 0;
        $this->pay_channel = $channel;
    }

    public function initPaymentByOrder($order, $channel) {
        $this->order_id = $order->id;
        $this->user_id = $order->userId;
        $this->uid = strRandom();
        $this->bill_amount = $order->finalAmount;
        $this->bill_currency = 'RMB';
        $this->bill_date = new \yii\db\Expression('NOW()');
        $this->payment_status = 0;
        $this->pay_channel = $channel;
    }

    /** getter and setter * */
    public function getBillAmount() {
        return $this->bill_amount;
    }

    public function getUid() {
        return $this->uid;
    }

    public function getBuyerAccount() {
        return $this->buyer_account;
    }

    public function setPingChargeId($v) {
        $this->ping_charge_id = $v;
    }

    public function setChannelTradeNo($v) {
        $this->channel_trade_no = $v;
    }

    public function setPaymentStatus($v) {
        $this->payment_status = $v;
    }

    public function setBuyerAccount($v) {
        $this->buyer_account = $v;
    }

    public function setPaidAmount($v) {
        $this->paid_amount = $v;
    }

    public function setPaidDate($v) {
        $this->paid_date = $v;
    }

}
