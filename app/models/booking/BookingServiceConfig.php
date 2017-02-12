<?php

namespace app\models\booking;
use app\models\EActiveRecord;
use Yii;

/**
 * This is the model class for table "booking_service_config".
 *
 * @property integer $id
 * @property string $service_name
 * @property string $deposit
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class BookingServiceConfig extends EActiveRecord
{
    const BOOKING_SERVICE_REGULAR = 1;//普通预约
    const BOOKING_SERVICE_FREE_LIINIC = 2;//义诊
    const BOOKING_SERVICE_ZERO_LIINIC = 3;//0元面诊
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'booking_service_config';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['deposit'], 'number'],
            [['date_created', 'date_updated', 'date_deleted'], 'safe'],
            [['service_name'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'service_name' => 'Service Name',
            'deposit' => 'Deposit',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        ];
    }
}
