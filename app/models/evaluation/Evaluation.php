<?php
namespace app\models\evaluation;
use app\models\EActiveRecord;
use Yii;
use yii\db\ActiveQuery;
/**
 * This is the model class for table "evaluation".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $user_name
 * @property integer $bk_id
 * @property string $disease_detail
 * @property integer $customer_service_evaluation
 * @property integer $platform_evaluation
 * @property integer $medical_evaluation
 * @property string $suggested_remarks
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class Evaluation extends EActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'evaluation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'bk_id', 'customer_service_evaluation', 'platform_evaluation', 'medical_evaluation', 'display_order'], 'integer'],
            [['date_created', 'date_updated', 'date_deleted'], 'safe'],
            [['user_name'], 'string', 'max' => 30],
            [['disease_detail'], 'string', 'max' => 200],
            [['suggested_remarks'], 'string', 'max' => 1000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'bk_id' => 'Bk ID',
            'disease_detail' => 'Disease Detail',
            'customer_service_evaluation' => 'Customer Service Evaluation',
            'platform_evaluation' => 'Platform Evaluation',
            'medical_evaluation' => 'Medical Evaluation',
            'suggested_remarks' => 'Suggested Remarks',
            'display_order' => 'Display Order',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        ];
    }
    public function getId() {
        return $this->id;
    }

    public function getUseriId() {
        return $this->user_id;
    }

    public function getUserName() {
        return $this->user_name;
    }

    public function getBkId() {
        return $this->bk_id;
    }

    public function getDiseaseDetail(){
        return $this->disease_detail;
    }

    public function getCustomerServiceEvaluation() {
        return $this->customer_service_evaluation;
    }

    public function getPlatformEvaluation() {
        return $this->platform_evaluation;
    }

    public function getMedicalEvaluation() {
        return $this->medical_evaluation;
    }

    public function getSuggestedRemarks() {
        return $this->suggested_remarks;
    }

    public function getDisplayOrder() {
        return $this->display_order;
    }

    /**
     * 根据BookingIds查询数据
     */
    public function getBookingIds($BookingIds, $attr = null, $with = null, $options = null){
        $criteria = new \yii\db\Query;
        if (is_array($with)) {
            $criteria->with = $with;
        }
        $criteria->from("evaluation");
        $criteria->andWhere('date_deleted is NULL');
        $criteria->andWhere('bk_id='.$BookingIds);
        return $criteria->one();
    }
    /**
     * 根据BookingId和用户ID查询数据
     */
    public function getInfobyuserid($id,$user_id, $attr = null, $with = null, $options = null){
        $criteria = new \yii\db\Query();
        if (is_array($with)) {
            $criteria->with = $with;
        }
        $criteria->from("evaluation");
        $criteria->andWhere('date_deleted is NULL');
        $criteria->andWhere('bk_id='.$id);
        if(!empty($user_id)){
            $criteria->andWhere('user_id='.$user_id);
        }
        return $criteria->one();
    }
}
