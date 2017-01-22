<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "core_rsa_config".
 *
 * @property integer $id
 * @property string $client
 * @property string $public_key
 * @property string $private_key
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class CoreRsaConfig extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'core_rsa_config';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client', 'public_key', 'private_key'], 'required'],
            [['public_key', 'private_key'], 'string'],
            [['date_created', 'date_updated', 'date_deleted'], 'safe'],
            [['client'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client' => 'Client',
            'public_key' => 'Public Key',
            'private_key' => 'Private Key',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        ];
    }
}
