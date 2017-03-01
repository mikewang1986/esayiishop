<?php
namespace app\models\disease;
use Yii;
use app\models\EActiveRecord;
/**
 * This is the model class for table "disease_category".
 *
 * @property integer $id
 * @property integer $cat_id
 * @property string $cat_name
 * @property integer $sub_cat_id
 * @property string $sub_cat_name
 * @property string $description
 * @property integer $expteam_id
 * @property string $app_version
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class DiseaseCategory extends EActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'disease_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_id', 'sub_cat_id', 'expteam_id', 'display_order'], 'integer'],
            [['date_created', 'date_updated', 'date_deleted'], 'safe'],
            [['cat_name', 'sub_cat_name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 100],
            [['app_version'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cat_id' => 'Cat ID',
            'cat_name' => 'Cat Name',
            'sub_cat_id' => 'Sub Cat ID',
            'sub_cat_name' => 'Sub Cat Name',
            'description' => 'Description',
            'expteam_id' => 'Expteam ID',
            'app_version' => 'App Version',
            'display_order' => 'Display Order',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiseases()
    {
        return $this->hasMany(Disease::className(), ['category_id' => 'id']);
    }

    public function getBySubCatId($sub_cat_id,$app_version = 7){
        $criteria =new \yii\db\ActiveQuery('app\models\disease\DiseaseCategory');
        $criteria->addCondition('date_deleted is NULL');
        $criteria->compare('sub_cat_id='. $sub_cat_id);
        $criteria->compare('app_version='.$app_version);
        $criteria->limit = 1;
        return $criteria->one();
    }
    public function getCategoryId() {
        return $this->cat_id;
    }

    public function getCategoryName() {
        return $this->cat_name;
    }

    public function getSubCategoryId() {
        return $this->sub_cat_id;
    }

    public function getSubCategoryName() {
        return $this->sub_cat_name;
    }

}
