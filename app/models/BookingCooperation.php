<?php
namespace app\models;
use app\components\StatCode;
use app\models\EActiveRecord;
use Yii;
/**
 * This is the model class for table "booking_cooperation".
 *
 * @property integer $id
 * @property string $ref_no
 * @property string $mobile
 * @property string $contact_name
 * @property integer $sex
 * @property integer $age
 * @property string $doctor_name
 * @property string $disease_detail
 * @property string $date_booking
 * @property string $backup
 * @property integer $source
 * @property string $user_host_ip
 * @property string $date_updated
 * @property string $date_deleted
 * @property string $date_created
 * @property string $nickname
 * @property string $dr_name
 * @property string $doctor_mobile
 * @property string $departments
 * @property string $hosptial_name
 * @property integer $customer_service_attitude
 * @property integer $medical_service
 * @property integer $platform_service
 * @property string $suggested_remarks
 */
class BookingCooperation extends EActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'booking_cooperation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile'], 'required'],
            [['sex', 'age', 'source', 'customer_service_attitude', 'medical_service', 'platform_service'], 'integer'],
            [['date_booking', 'date_updated', 'date_deleted', 'date_created'], 'safe'],
            [['ref_no'], 'string', 'max' => 14],
            [['mobile', 'doctor_mobile'], 'string', 'max' => 20],
            [['contact_name', 'doctor_name'], 'string', 'max' => 50],
            [['disease_detail', 'backup'], 'string', 'max' => 1000],
            [['user_host_ip', 'dr_name', 'departments', 'hosptial_name', 'suggested_remarks'], 'string', 'max' => 255],
            [['nickname'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ref_no' => 'Ref No',
            'mobile' => '联系电话',
            'contact_name' => '患者名称',
            'sex' => '性别',
            'age' => '年龄',
            'doctor_name' => '患者姓名',
            'disease_detail' => '已知症端',
            'date_booking' => '预约日期',
            'backup' => '备注',
            'source' => '来源',
            'user_host_ip' => 'User Host Ip',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
            'date_created' => 'Date Created',
            'nickname' => '微博昵称',
            'dr_name' => '医生名称',
            'doctor_mobile' => '医生电话',
            'departments' => '科室',
            'hosptial_name' => '医院',
        ];
    }
    public function getOptionsGender() {
        return array(
            StatCode::GENDER_MALE => '男',
            StatCode::GENDER_FEMALE => '女'
        );
    }

    public function getGender($text = true) {
        if ($text) {
            $options = $this->getOptionsGender();
            if (isset($options[$this->sex]))
                return $options[$this->sex];
            else
                return '';
        }else {
            return $this->sex;
        }
    }
    //add source content
    public function getOptionsSource() {
        return array(
            1=> '安庆医保项目',
            2 => '新浪爱问',
            3=>'麻城',
        );
    }

    public function getSoure($text = true) {
        if ($text) {
            $options = $this->getOptionsSource();
            if (isset($options[$this->source]))
                return $options[$this->source];
            else
                return '';
        }else {
            return $this->source;
        }
    }
}
