<?php

namespace app\models\topic;
use app\models\EActiveRecord;
use Yii;

/**
 * This is the model class for table "special_topic".
 *
 * @property integer $id
 * @property string $topic
 * @property string $content_url
 * @property string $banner_url
 * @property integer $like_count
 * @property string $date_published
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * @property SpecialTopicUserLike[] $specialTopicUserLikes
 */
class SpecialTopic extends EActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'special_topic';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['topic'], 'required'],
            [['like_count', 'display_order'], 'integer'],
            [['date_published', 'date_created', 'date_updated', 'date_deleted'], 'safe'],
            [['topic'], 'string', 'max' => 200],
            [['content_url', 'banner_url'], 'string', 'max' => 1000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '专题id',
            'topic' => '专题名',
            'content_url' => '专题页面链接',
            'banner_url' => '专题图片链接',
            'like_count' => 'Like Count',
            'date_published' => '发布日期',
            'display_order' => 'Display Order',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecialTopicUserLikes()
    {
        return $this->hasMany(SpecialTopicUserLike::className(), ['special_topic_id' => 'id']);
    }
}
