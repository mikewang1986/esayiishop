<?php
namespace app\models\patient;
use app\models\EActiveRecord;
use Yii;
use app\models\patient\PatientInfo;
use app\models\user\User;
use app\models\sales\SalesOrder;
/**
 * This is the model class for table "patient_booking".
 *
 * @property integer $id
 * @property string $ref_no
 * @property integer $doctor_id
 * @property string $doctor_name
 * @property integer $patient_id
 * @property string $patient_name
 * @property integer $creator_id
 * @property string $creator_name
 * @property string $expected_doctor
 * @property string $expected_hospital
 * @property string $expected_dept
 * @property integer $status
 * @property integer $operation_finished
 * @property integer $travel_type
 * @property string $date_start
 * @property string $date_end
 * @property string $detail
 * @property integer $is_deposit_paid
 * @property string $appt_date
 * @property string $date_confirm
 * @property string $user_agent
 * @property string $cs_explain
 * @property integer $doctor_accept
 * @property string $doctor_opinion
 * @property string $remark
 * @property string $summary_file_status
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class PatientBooking extends EActiveRecord
{
    const BK_STATUS_NEW = 1;         // 待处理
    const BK_STATUS_PROCESSING = 2;   // 处理中
    const BK_STATUS_CONFIRMED_DOCTOR = 3;   // 已确认专家
    //const BK_STATUS_PATIENT_ACCEPTED = 4;   // 患者已接受
    const BK_STATUS_INVALID = 7;        // 失效的
    const BK_STATUS_SURGER_DONE = 8;        // 已完成手术
    const BK_STATUS_DC_ACCEPTED = 9;          // 已收到出院小结
    const BK_STATUS_CANCELLED = 99;   // 已取消
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'patient_booking';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['doctor_id', 'patient_id', 'creator_id', 'status', 'operation_finished', 'travel_type', 'is_deposit_paid', 'doctor_accept', 'summary_file_status'], 'integer'],
            [['patient_id', 'creator_id', 'travel_type'], 'required'],
            [['date_start', 'date_end', 'appt_date', 'date_confirm', 'date_created', 'date_updated', 'date_deleted'], 'safe'],
            [['ref_no'], 'string', 'max' => 14],
            [['doctor_name', 'patient_name', 'creator_name', 'user_agent'], 'string', 'max' => 20],
            [['expected_doctor'], 'string', 'max' => 200],
            [['expected_hospital', 'expected_dept'], 'string', 'max' => 50],
            [['detail'], 'string', 'max' => 1000],
            [['cs_explain', 'doctor_opinion', 'remark'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ref_no' => '预约号',
            'patient_id' => '患者',
            'patient_name' => '患者',
            'creator_id' => '创建者',
            'creator_name' => '创建者',
            'doctor_id' => '预约医生',
            'doctor_name' => '预约医生',
            'status' => '状态',
            'travel_type' => '出行方式',
            'date_start' => '开始日期',
            'date_end' => '结束日期',
            'detail' => '细节',
            'is_deposit_paid' => '是否支付定金',
            'appt_date' => '最终预约日期',
            'date_confirm' => '预约确认日期',
            'remark' => '备注',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        ];
    }
    //查询创建者旗下所有的患者
    public function getAllByCreatorId($creatorId, $attributes = '*', $with = null, $options = null) {
        return $this->getAllByAttributes(array('creator_id' => $creatorId), $with, $options);
    }

    //查询该创建者旗下的患者信息
    public function getByIdAndCreatorId($id, $creatorId, $attributes = '*', $with = null) {
        return $this->getByAttributes(array('id' => $id, 'creator_id' => $creatorId), $with);
    }

    //根据外键查询booking
    public function getByPatientId($patientId, $attributes = '*', $with = null) {
        return $this->getByAttributes(array('patient_id' => $patientId), $with);
    }

    //查询预约该医生的患者列表
    public function getAllByDoctorId($doctorId, $attributes = '*', $with = null, $options = null) {
        return $this->getAllByAttributes(array('doctor_id' => $doctorId), $with, $options);
    }

    //查询预约该医生的患者详细信息
    public function getByIdAndDoctorId($id, $doctorId, $attributes = '*', $with = null) {
        return $this->getByAttributes(array('id' => $id, 'doctor_id' => $doctorId), $with);
    }

    /*     * ****** Accessors ******* */
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatient()
    {
        return $this->hasOne(PatientInfo::className(), ['id' => 'doctor_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(User::className(), ['id' => 'doctor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(SalesOrder::className(), ['id' => 'hp_dept_id']);
    }

   // public function getPatient() {
       // return $this->pbPatient;
   // }



  /*  public function getDoctor() {
        return $this->pbDoctor;
    }*/

   /* public function getOrder() {
        return $this->pbOrder;
    }*/

    public function getId() {
        return $this->id;
    }

    public function getRefNo() {
        return $this->ref_no;
    }

    public function getPatientId() {
        return $this->patient_id;
    }

    public function getCreatorId() {
        return $this->creator_id;
    }

    public function getDoctorId() {
        return $this->doctor_id;
    }

    public function getCreatorName() {
        return $this->creator_name;
    }

    public function getPatientName() {
        return $this->patient_name;
    }

    public function getDoctorName() {
        return $this->doctor_name;
    }

    public function getOptionsBkStatus() {
        return array(
            self::BK_STATUS_NEW => '待处理',
            self::BK_STATUS_PROCESSING => '处理中',
            self::BK_STATUS_CONFIRMED_DOCTOR => '已确认专家',
            //    self::BK_STATUS_PATIENT_ACCEPTED => '患者已接受',
            self::BK_STATUS_SURGER_DONE => '已完成手术',
            self::BK_STATUS_DC_ACCEPTED => '收到出院小结',
            self::BK_STATUS_CANCELLED => '已取消',
            self::BK_STATUS_INVALID => '失效的'
        );
    }

    public function getStatus($text = true) {
        if ($text) {
            $options = self::getOptionsBkStatus();
            if (isset($options[$this->status])) {
                return $options[$this->status];
            } else {
                return StatCode::ERROR_UNKNOWN;
            }
        } else {
            return $this->status;
        }
    }

    public function getTravelType($text = true) {
        if ($text) {
            return StatCode::getBookingTravelType($this->travel_type);
        } else {
            return $this->travel_type;
        }
    }

    public function getDateStart($format = null) {
        return $this->getDateAttribute($this->date_start, $format);
    }

    public function getDateEnd($format = null) {
        return $this->getDateAttribute($this->date_end, $format);
    }

    public function getDetail($ntext = true) {
        return $this->getTextAttribute($this->detail, $ntext);
    }

    public function getApptdate($format = null) {
        return $this->getDateAttribute($this->appt_date, $format);
    }

    public function getDateConfirm($format = null) {
        return $this->getDatetimeAttribute($this->date_confirm, $format);
    }

    public function getRemark($ntext = true) {
        return $this->getTextAttribute($this->remark, $ntext);
    }

    public function getIsDepositPaid($text = false) {
        if ($text) {
            return StatCode::getPaymentStatus($this->is_deposit_paid);
        } else {
            return $this->is_deposit_paid;
        }
    }

    public function getUserAgent() {
        return $this->user_agent;
    }

    public function setStatus($v) {
        $this->status = $v;
    }

    public function setCreatorId($v) {
        $this->creator_id = $v;
    }

    public function setPatientId($v) {
        $this->patient_id = $v;
    }

    public function setDoctorId($v) {
        $this->doctor_id = $v;
    }

    public function setCreatorName($v) {
        $this->creator_name = $v;
    }

    public function setPatientName($v) {
        $this->patient_name = $v;
    }

    public function setDoctorName($v) {
        $this->doctor_name = $v;
    }

    /*     * ****** Private Methods ******* */

    private function createRefNumber() {
        if ($this->isNewRecord) {
            $flag = true;
            while ($flag) {
                $refNumber = 'PB' . date("ymd") . str_pad(mt_rand(0, 999999), 6, "0", STR_PAD_LEFT);
                if(arrayNotEmpty($this->getAttributes(array("ref_no"=>$refNumber)))){
               // if ($this->exists('t.ref_no =:refno', array(':refno' => $refNumber)) == false) {
                    $this->ref_no = $refNumber;
                    $flag = false;
                }
            }
        }
    }
}
