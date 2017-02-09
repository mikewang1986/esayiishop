<?php

namespace app\models\payment;
use app\models\EActiveRecord;
use Yii;

/**
 * This is the model class for table "core_log_payment".
 *
 * @property integer $id
 * @property string $level
 * @property string $category
 * @property string $request_url
 * @property string $message
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class CoreLogPayment extends EActiveRecord
{
    const LEVEL_TRACE = 'trace';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';
    const LEVEL_INFO = 'info';
    const LEVEL_PROFILE = 'profile';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'core_log_payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message'], 'string'],
            [['date_created', 'date_updated', 'date_deleted'], 'safe'],
            [['level', 'category'], 'string', 'max' => 128],
            [['request_url'], 'string', 'max' => 2048]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'level' => 'Level',
            'category' => 'Category',
            'request_url' => 'Request Url',
            'message' => 'Message',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        ];
    }
    public static function log($msg, $level, $requestUrl = null, $category = __METHOD__) {
        $log = new CoreLogPayment();
        $log->message = $msg;
        $log->level = $level;
        $log->request_url = isset($requestUrl) ? $requestUrl : Yii::$app->request->url;
        $log->category = $category;
        return $log->save();
    }
}
