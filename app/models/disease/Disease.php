<?php
namespace app\models\disease;
use Yii;
use app\models\EActiveRecord;
/**
 * This is the model class for table "disease".
 *
 * @property integer $id
 * @property string $name
 * @property integer $category_id
 * @property string $description
 * @property integer $level
 * @property string $basic_price
 * @property string $app_version
 * @property integer $display_order
 * @property integer $ranking
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class Disease extends EActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'disease';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['category_id', 'level', 'display_order', 'ranking'], 'integer'],
            [['basic_price'], 'number'],
            [['date_created', 'date_updated', 'date_deleted'], 'safe'],
            [['name'], 'string', 'max' => 20],
            [['description'], 'string', 'max' => 500],
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
            'name' => 'Name',
            'category_id' => 'Category',
            'description' => 'Description',
            'display_order' => 'Display Order',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        ];
    }

    public function getByName($name,$app_version = 7) {
        $criteria = new \yii\db\ActiveQuery('app\models\disease\Disease');
        $criteria->andOnCondition('date_deleted is NULL');
        $criteria->andOnCondition('name="'.$name.'"');
        $criteria->andOnCondition('app_version='.$app_version);
        $criteria->limit = 1;
        return $criteria->one();
    }
    /*     * ****** Accessors ******* */

    public function getName() {
        return $this->name;
    }

    public function getDescription() {
        return $this->description;
    }
}
