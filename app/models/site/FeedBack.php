<?php

namespace app\models\site;
use Yii;
use app\models\EActiveRecord;

/**
 * This is the model class for table "feed_back".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $user_host_ip
 * @property string $source
 * @property string $contact_mobile
 * @property string $content
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class FeedBack extends EActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feed_back';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['date_created', 'date_updated', 'date_deleted'], 'safe'],
            [['user_host_ip', 'source', 'contact_mobile'], 'string', 'max' => 20],
            [['content'], 'string', 'max' => 1000]
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
            'user_host_ip' => 'User Host Ip',
            'source' => 'Source',
            'contact_mobile' => 'Contact Mobile',
            'content' => 'Content',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        ];
    }
}
