<?php
namespace app\models\patient;
use app\models\EActiveRecord;
use app\components\StatCode;
use Yii;

/**
 * This is the model class for table "patient_info".
 *
 * @property integer $id
 * @property integer $creator_id
 * @property string $name
 * @property string $mobile
 * @property integer $birth_year
 * @property integer $birth_month
 * @property integer $age
 * @property integer $age_month
 * @property integer $gender
 * @property integer $country_id
 * @property integer $state_id
 * @property string $state_name
 * @property integer $city_id
 * @property string $city_name
 * @property string $disease_name
 * @property string $disease_detail
 * @property integer $purpose
 * @property string $remark
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class PatientInfo extends EActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'patient_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['creator_id', 'name', 'birth_year', 'gender'], 'required'],
            [['creator_id', 'birth_year', 'birth_month', 'age', 'age_month', 'gender', 'country_id', 'state_id', 'city_id', 'purpose'], 'integer'],
            [['date_created', 'date_updated', 'date_deleted'], 'safe'],
            [['name', 'disease_name'], 'string', 'max' => 50],
            [['mobile'], 'string', 'max' => 11],
            [['state_name', 'city_name'], 'string', 'max' => 20],
            [['disease_detail'], 'string', 'max' => 1000],
            [['remark'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'creator_id' => '所属医生',
            'name' => '姓名',
            'birth_year' => '出生年份',
            'birth_year' => '出生月份',
            'age' => '年龄',
            'age_month' => '年龄余下的月数',
            'gender' => '性别',
            'mobile' => '手机号码',
            'country_id' => '所在国家',
            'state_id' => '所在省份',
            'state_name' => '所在省份',
            'city_id' => '所在城市',
            'city_name' => '所在城市',
            'disease_name' => '疾病诊断',
            'disease_detail' => '病史描述',
            'remark' => '备注',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        ];
    }
    public function getByIdAndCreatorId($id, $creatorId, $attributes = '*', $with = null, $options = null) {
        return $this->getByAttributes(array('id' => $id, 'creator_id' => $creatorId), $with, $options);
    }

    //查询患者列表
    public function getAllByCreateorId($creatorId, $attributes = '*', $with = null, $options = null) {
        return $this->getAllByAttributes(array('creator_id' => $creatorId), $with, $options);
    }

    /*     * ****** Accessors ******* */

    public function getCreator() {
        return $this->patientCreator;
    }

    public function getPatientMR() {
        return $this->patientMR;
    }

    public function getMR() {
//        $model = new Patient();
//        $model->setAttributes(array('id'=>$this->getId(),'disease_name'=>$this->getDiseaseName(),'disease_detail'=>$this->getDiseaseDetail()));
//        return  $model;
    }

    public function getMRFiles() {
        return $this->patientMRFiles;
    }

    public function getCreatorId() {
        return $this->creator_id;
    }

    public function getBookings() {
        return $this->patientBookings;
    }

    public function getStateName() {
        if (strIsEmpty($this->state_name) === false) {
            return $this->state_name;
        } elseif (isset($this->patientState)) {
            return $this->patientState->getName();
        } else {
            return '';
        }
    }

    public function getCityName() {
        if (strIsEmpty($this->city_name) === false) {
            return $this->city_name;
        } elseif (isset($this->patientCity)) {
            return $this->patientCity->getName();
        } else {
            return '';
        }
    }

    public function getDateCreated($format = 'm-d') {
        $date = new \DateTime($this->date_created);
        return $date->format($format);
    }

    public function getName() {
        return $this->name;
    }

    public function getBirthYear() {
        return $this->birth_year;
    }

    public function getBirthMonth() {
        return $this->birth_month;
    }

    public function setBirthYear($v) {
        if ($v < 150) {
            // 年龄
            $yearNow = date('Y');
            $this->birth_year = $yearNow - $v;
        } else {
            // 出生年份
            $this->birth_year = $v;
        }
    }

    public function setAge() {
        $timeStart = $this->getBirthYear() . '-' . $this->getBirthMonth();
        $timeStart = date_create($timeStart);
        $timeEnd = date_create(date('Y-m'));
        $interval = date_diff($timeStart, $timeEnd);
        $this->age = $interval->y;
        $this->age_month = $interval->m;
    }

    public function getAge() {
        return $this->age;
    }

    public function getAgeMonth() {
        if (is_null($this->age_month)) {
            return 0;
        }
        return $this->age_month;
    }

    public function getGender($text = true) {
        if ($text) {
            $options = StatCode::getOptionsGender();
            if (isset($options[$this->gender]))
                return $options[$this->gender];
            else
                return '';
        }else {
            return $this->gender;
        }
    }

    public function getMobile(){
        return $this->mobile;
    }

    public function getDiseaseName() {
        return $this->disease_name;
    }

    public function getDiseaseDetail($ntext = true) {
        return $this->getTextAttribute($this->disease_detail, $ntext);
    }

    public function getRemark($ntext = true) {
        return $this->getTextAttribute($this->remark, $ntext);
    }
}
