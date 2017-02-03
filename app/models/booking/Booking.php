<?php
namespace app\models\booking;
use Yii;
use app\models\EActiveRecord;
use app\components\StatCode;
/**
 * This is the model class for table "booking".
 *
 * @property integer $id
 * @property string $ref_no
 * @property integer $doctor_user_id
 * @property string $doctor_user_name
 * @property string $mobile
 * @property string $contact_name
 * @property string $contact_email
 * @property integer $bk_status
 * @property integer $bk_type
 * @property integer $doctor_id
 * @property string $doctor_name
 * @property integer $expteam_id
 * @property string $expteam_name
 * @property integer $city_id
 * @property integer $hospital_id
 * @property string $hospital_name
 * @property integer $hp_dept_id
 * @property string $hp_dept_name
 * @property string $disease_name
 * @property string $disease_detail
 * @property string $date_start
 * @property integer $user_id
 * @property string $date_end
 * @property string $appt_date
 * @property integer $is_deposit_paid
 * @property string $user_agent
 * @property string $remark
 * @property integer $is_corporate
 * @property string $corporate_name
 * @property string $corp_staff_rel
 * @property string $submit_via
 * @property integer $is_vendor
 * @property integer $vendor_id
 * @property integer $vendor_site
 * @property string $vendor_trade_no
 * @property integer $is_commonweal
 * @property integer $booking_service_id
 * @property integer $doctor_accept
 * @property string $doctor_opinion
 * @property string $cs_explain
 * @property string $date_updated
 * @property string $date_deleted
 * @property string $date_created
 *
 * @property RegionCity $city
 * @property Doctor $doctor
 * @property User $doctorUser
 * @property ExpertTeam $expteam
 * @property Hospital $hospital
 * @property HospitalDepartmentCopy1 $hpDept
 * @property User $user
 * @property BookingFile[] $bookingFiles
 */
class Booking extends EActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'booking';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ref_no', 'mobile'], 'required'],
            [['doctor_user_id', 'bk_status', 'bk_type', 'doctor_id', 'expteam_id', 'city_id', 'hospital_id', 'hp_dept_id', 'user_id', 'is_deposit_paid', 'is_corporate', 'is_vendor', 'vendor_id', 'vendor_site', 'is_commonweal', 'booking_service_id', 'doctor_accept'], 'integer'],
            [['date_start', 'date_end', 'appt_date', 'date_updated', 'date_deleted', 'date_created'], 'safe'],
            [['ref_no'], 'string', 'max' => 14],
            [['doctor_user_name', 'contact_name', 'doctor_name', 'expteam_name', 'hospital_name', 'hp_dept_name', 'disease_name', 'corporate_name', 'corp_staff_rel'], 'string', 'max' => 50],
            [['mobile'], 'string', 'max' => 11],
            [['contact_email'], 'string', 'max' => 100],
            [['disease_detail'], 'string', 'max' => 1000],
            [['user_agent'], 'string', 'max' => 20],
            [['remark', 'doctor_opinion', 'cs_explain'], 'string', 'max' => 500],
            [['submit_via'], 'string', 'max' => 10],
            [['vendor_trade_no'], 'string', 'max' => 32],
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
            'ref_no' => '预约号',
            'doctor_user_id' => '用户',
          //  'doctor_user_name' => 'Doctor User Name',
            'mobile' => '手机号',
            'contact_name' => '患者姓名',
            'contact_email' => '邮箱',
            'bk_status' => '状态',
            'bk_type' => '种类',
            'doctor_id' => '医生',
            'doctor_name' => '医生姓名',
            'expteam_id' => '专家团队',
            'expteam_name' => '专家团队',
            'city_id' => '城市',
            'hospital_id' => '医院',
            'hospital_name' => '医院名称',
            'hp_dept_id' => '科室',
            'hp_dept_name' => '科室名称',
            'disease_name' => '疾病诊断',
            'disease_detail' => '病情',
            'date_start' => 'Date Start',
            'user_id' => 'User ID',
            'date_end' => 'Date End',
            'appt_date' => 'Appt Date',
           // 'is_deposit_paid' => 'Is Deposit Paid',
            'user_agent' => '数据来源',
            'remark' => 'Remark',
            'is_corporate' => '是否是企业用户',
            'corporate_name' => '企业名称',
            'corp_staff_rel' => '与患者的关系',
            'submit_via' => 'Submit Via',
           // 'is_vendor' => 'Is Vendor',
           // 'vendor_id' => 'Vendor ID',
           // 'vendor_site' => 'Vendor Site',
           // 'vendor_trade_no' => 'Vendor Trade No',
           // 'is_commonweal' => 'Is Commonweal',
            //'booking_service_id' => 'Booking Service ID',
           // 'doctor_accept' => 'Doctor Accept',
          //  'doctor_opinion' => 'Doctor Opinion',
         //   'cs_explain' => 'Cs Explain',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
            'date_created' => '创建日期',
            'expertBooked' => '所约专家',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCitys()
    {
        return $this->hasOne(RegionCity::className(), ['id' => 'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctors()
    {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorUser()
    {
        return $this->hasOne(User::className(), ['id' => 'doctor_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExpteam()
    {
        return $this->hasOne(ExpertTeam::className(), ['id' => 'expteam_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHospitals()
    {
        return $this->hasOne(Hospital::className(), ['id' => 'hospital_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHpDepts()
    {
        return $this->hasOne(HospitalDepartmentCopy1::className(), ['id' => 'hp_dept_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookingFiles()
    {
        return $this->hasMany(BookingFile::className(), ['booking_id' => 'id']);
    }
    /**
     * 根据bookingid查询数据
     * @param type $BookingIds
     * @param type $with
     * @return type
     */
    public function getAllByIds($BookingIds, $attr = null, $with = null, $options = null) {
       /* $criteria = new CDbCriteria;
        if (is_array($with)) {
            $criteria->with = $with;
        }
        //$criteria->join = 'LEFT JOIN `booking_file` bookingFile ON (t.id=bookingFile.booking_id)';
        $criteria->addCondition('date_deleted is NULL');
       // $criteria->addInCondition('id', $BookingIds);
        return $this->findAll($criteria);*/
        $criteria =new \yii\db\ActiveQuery('app\models\booking\Booking');
        if (is_array($with)) {
            $criteria->with($with);
        }
        $criteria->andWhere('date_deleted is NULL');
        $criteria->andOnCondition('id in ('.implode(",",$BookingIds).')');
        return $criteria->all();
    }

    public function beforeValidate() {
        $this->createRefNumber();
        return parent::beforeValidate();
    }

    /*public function beforeSave() {

        return parent::beforeSave();
    }*/
    /*     * ****** Query Methods ******* */

    public function getByRefNo($refno) {
        return $this->getByAttributes(array('ref_no' => $refno));
    }

    public function getByIdAndUserId($id, $userId, $with = null) {
        return $this->getByAttributes(array('id' => $id, 'user_id' => $userId), $with);
    }

    public function getByIdAndUser($id, $userId, $mobile, $with = null) {
        $criteria =new \yii\db\ActiveQuery('app\models\booking\Booking');
        $criteria->andWhere('date_deleted is NULL');
        $criteria->andWhere('t.id='.$id.'  AND( user_id='.$userId.' OR mobile="'.$mobile.'")');
        if (isset($with) && is_array($with))
            $criteria->with($with);
        return $criteria->all();
    }
    public function getAllByUserIdOrMobile($userId, $mobile, $with = null, $options = null, $bk_status = null, $vendorId = null, $isWap = false) {
       /* $criteria = new CDbCriteria();
        $criteria->compare("t.user_id", $userId, false, 'AND');
        $criteria->compare("t.mobile", $mobile, false, 'OR');
        if ($bk_status) {
            if ($isWap) {
                $criteria->compare("t.bk_status", $bk_status, false, 'AND');
            } else {
                if ($bk_status == 6 || $bk_status == 8) {
                    $criteria->compare("t.bk_status", 6, false, 'OR');
                    $criteria->compare("t.bk_status", 8, false, 'AND');
                } else {
                    $criteria->compare("t.bk_status", $bk_status, false, 'AND');
                }
            }
        }
        if ($vendorId) {
            $criteria->compare("t.vendor_id", $vendorId, false, 'AND');
        }
        $criteria->addCondition("t.date_deleted is NULL");
        if (isset($with) && is_array($with))
            $criteria->with = $with;
        if (isset($options['offset']))
            $criteria->offset = $options['offset'];
        if (isset($options['limit']))
            $criteria->limit = $options['limit'];
        if (isset($options['order']))
            $criteria->order = $options['order'];

        return $this->findAll($criteria);*/

        $criteria =new \yii\db\ActiveQuery('app\models\booking\Booking');
        $criteria->from('booking');
        $criteria->andWhere('user_id='.$userId);
        $criteria->orWhere('mobile='.$mobile);
        if ($bk_status) {
            if ($isWap) {
                $criteria->andWhere('bk_status='.$bk_status);
            } else {
                if ($bk_status == 6 || $bk_status == 8) {
                    $criteria->orWhere('bk_status=6');
                    $criteria->andWhere('bk_status=8');
                   // $criteria->compare("t.bk_status", 6, false, 'OR');
                    //$criteria->compare("t.bk_status", 8, false, 'AND');
                } else {
                    $criteria->andWhere('bk_status='.$bk_status);
                   // $criteria->compare("t.bk_status", $bk_status, false, 'AND');
                }
            }
        }
        if ($vendorId) {
            $criteria->andWhere('vendor_id='.$vendorId);
            //$criteria->compare("t.vendor_id", $vendorId, false, 'AND');
        }
        $criteria->andWhere("date_deleted is NULL");
        if (isset($with) && is_array($with))
            $criteria->with($with);
        if (isset($options['offset']))
            $criteria->offset($options['offset']);
        if (isset($options['limit']))
            $criteria->limit( $options['limit']);
        if (isset($options['order']))
            $criteria->orderBy($options['order']);
        return $criteria->all();

    }

    public function getBookByUserIdOrMobileAndStatus($userId, $mobile, $with = null, $options = null) {
        $criteria = new CDbCriteria();
        $criteria->compare("t.user_id", $userId, false, 'AND');
        $criteria->compare("t.mobile", $mobile, false, 'OR');
        $criteria->addCondition("t.date_deleted is NULL");
        if (isset($with) && is_array($with))
            $criteria->with = $with;
        if (isset($options['offset']))
            $criteria->offset = $options['offset'];
        if (isset($options['limit']))
            $criteria->limit = $options['limit'];
        if (isset($options['order']))
            $criteria->order = $options['order'];

        return $this->findAll($criteria);
    }

    public function setIsCorporate($v = 1) {
        $this->is_corporate = $v;
    }

    public function getOptionsStatus() {
        return StatCode::getOptionsBookingStatus();

    }
    /*     * ****** Private Methods ******* */

    private function createRefNumber() {
        if ($this->isNewRecord) {
            $flag = true;
            while ($flag) {
                $refNumber = $this->getRefNumberPrefix() . date("ymd") . str_pad(mt_rand(0, 999999), 6, "0", STR_PAD_LEFT);
                if ($this->exists('t.ref_no =:refno', array(':refno' => $refNumber)) == false) {
                    $this->ref_no = $refNumber;
                    $flag = false;
                }
            }
        }
    }

    /**
     * Return ref_no prefix charactor based on bk_type
     * default 'AA' is an eception charactor
     * @return string
     */
    private function getRefNumberPrefix() {
        switch ($this->bk_type) {
            case StatCode::BK_TYPE_DOCTOR :
                return "DR";
            case StatCode::BK_TYPE_EXPERTTEAM :
                return "ET";
            case StatCode::BK_TYPE_DEPT :
                return "HP";
            case StatCode::BK_TYPE_QUICKBOOK :
                return "QB";
            case StatCode::BK_TYPE_WIFI :
                return "WF";
            default:
                return "AA";
        }
    }

    /*     * ****** Accessors ******* */

    public function getExpertBooked() {
        if ($this->bk_type == StatCode::BK_TYPE_EXPERTTEAM) {
            return $this->getExpertteam();
        } elseif ($this->bk_type == StatCode::BK_TYPE_DOCTOR) {
            return $this->getDoctor();
        } elseif ($this->bk_type == StatCode::BK_TYPE_QUICKBOOK) {
            return $this->doctor_name;
        } else {
            return null;
        }
    }

    public function getExpertNameBooked() {
        if (!is_object($this->getExpertBooked())) {
            return $this->getExpertBooked();
        } elseif ($this->getExpertBooked() !== null) {
            return $this->getExpertBooked()->getName();
        } else {
            return $this->doctor_name;
        }
    }

    public function getOwner() {
        return $this->bkOwner;
    }

    public function getBkFiles() {
        return $this->bkFiles;
    }

    public function getDoctor() {
        return $this->getDoctors()->one();
    }

    public function getExpertTeam() {
        return $this->bkExpertTeam;
    }

    public function getHospital() {
        return $this->bkHospital;
    }

    public function getHpDept() {
        return $this->bkHpDept;
    }

    public function getCity() {
        return $this->bkCity;
    }

    public function getRefNo() {
        return $this->ref_no;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getMobile() {
        return $this->mobile;
    }

    public function getContactName() {
        return $this->contact_name;
    }

    public function getBkStatus($v = true) {
        if ($v) {
            return StatCode::getBookingStatus($this->bk_status);
        } else {
            return $this->bk_status;
        }
    }

    public function getBkStatusCode() {
        return $this->bk_status;
    }

    public function setBkStatus($v) {
        $this->bk_status = $v;
    }

    public function getBookingType() {
        return StatCode::getBookingType($this->bk_type);
    }

    public function getBkType() {
        return $this->bk_type;
    }

    public function getDoctorName() {
        if (strIsEmpty($this->doctor_name) === false) {
            return $this->doctor_name;
        } elseif (isset($this->doctor_id) && $this->getDoctor() !== null) {
            return $this->getDoctor()->getName();
        } else {
            return '';
        }
    }

    public function getExpertteamId() {
        return $this->expteam_id;
    }

    public function getExpertteamName() {
        if (strIsEmpty($this->expteam_name) === false) {
            return $this->expteam_name;
        } elseif (isset($this->expteam_id) && $this->getExpertTeam() !== null) {
            return $this->getExpertTeam()->getName();
        } else {
            return '';
        }
    }

    public function getHospitalName() {
        if (strIsEmpty($this->hospital_name) === false) {
            return $this->hospital_name;
        } elseif (isset($this->hospital_id) && $this->getHospital() !== null) {
            return $this->getHospital()->getName();
        } else {
            return '';
        }
    }

    public function getHpDeptName() {
        if (strIsEmpty($this->hp_dept_name) === false) {
            return $this->hp_dept_name;
        } elseif (isset($this->hp_dept_id) && $this->getHpDept() !== null) {
            return $this->getHpDept()->getName();
        } else {
            return '';
        }
    }

    public function getDiseaseName() {
        return $this->disease_name;
    }

    public function getDiseaseDetail($ntext = true) {
        return $this->getTextAttribute($this->disease_detail, $ntext);
    }

    public function getDateStart($format = null) {
        return $this->getDateAttribute($this->date_start, $format);
    }

    public function getDateEnd($format = null) {
        return $this->getDateAttribute($this->date_end, $format);
    }

    public function getApptDate($format = null) {
        return $this->getDatetimeAttribute($this->appt_date, $format);
    }

    public function getRemark($ntext = true) {
        return $this->getTextAttribute($this->remark, $ntext);
    }

    public function getIsCorporate() {
        return $this->is_corporate;
    }

    public function getCorporateName() {
        return $this->corporate_name;
    }

    public function getCorpStaffRef() {
        return $this->corp_staff_rel;
    }

    public function getUserAgent() {
        return $this->user_agent;
    }
    /**
     * 获得用户手术单个状态数量
     * @param unknown $userId
     */
    public function getCountBkStatusByUserId($userId) {
        $criteria = new CDbCriteria();
        $criteria->select = 't.bk_status,count(t.bk_status) num'; //默认*
        $criteria->addCondition("t.user_id=" . $userId);
        $criteria->addCondition("t.date_deleted is NULL");
        $criteria->group = 't.bk_status';
        $this->num = null;
        return $this->findAll($criteria);
    }

    /**
     * 根据用户ID和手术单状态获得匹配的手术单信息
     * @param unknown $userId
     * @param unknown $bkStatus
     */
    public function getBookingByUserIdAndBkStatus($userId, $bkStatus) {
        $criteria = new CDbCriteria();
        $criteria->addCondition("t.user_id=" . $userId, "and");
        $criteria->addCondition("t.bk_status=" . $bkStatus);
        $criteria->addCondition("t.date_deleted is NULL");
        return $this->findAll($criteria);
    }

    public function getBookingByMobileORUserId($userId, $mobile) {
        $criteria = new CDbCriteria();
        $criteria->select = 't.bk_status,count(t.bk_status) num'; //默认*
        $criteria->addCondition('t.user_id=' . $userId . ' OR t.mobile=' . $mobile);
        $criteria->addCondition("t.date_deleted is NULL");
        $criteria->group = 't.bk_status';
        $this->num = null;
        return $this->findAll($criteria);
    }

    public function getBookingByMobileORUserIdANDBkId($userId, $mobile, $id) {
        $criteria = new CDbCriteria();
        $criteria->addCondition("t.user_id=" . $userId . " OR t.mobile=" . $mobile, "AND");
        $criteria->addCondition("t.id=" . $id, "AND");
        $criteria->addCondition("t.date_deleted is NULL");
        return $this->find($criteria);
    }
}
