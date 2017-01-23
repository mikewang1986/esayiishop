<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_doctor_cert".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $cert_type
 * @property string $uid
 * @property string $file_ext
 * @property string $mime_type
 * @property string $file_name
 * @property string $file_url
 * @property integer $file_size
 * @property string $thumbnail_name
 * @property string $thumbnail_url
 * @property string $base_url
 * @property integer $has_remote
 * @property string $remote_domain
 * @property string $remote_file_key
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * @property User $user
 */
class UserDoctorCert extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_doctor_cert';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'uid', 'file_ext', 'file_url'], 'required'],
            [['user_id', 'cert_type', 'file_size', 'has_remote', 'display_order'], 'integer'],
            [['date_created', 'date_updated', 'date_deleted'], 'safe'],
            [['uid'], 'string', 'max' => 32],
            [['file_ext'], 'string', 'max' => 10],
            [['mime_type'], 'string', 'max' => 20],
            [['file_name', 'thumbnail_name', 'remote_file_key'], 'string', 'max' => 40],
            [['file_url', 'thumbnail_url', 'base_url', 'remote_domain'], 'string', 'max' => 255]
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
            'cert_type' => 'Cert Type',
            'uid' => 'Uid',
            'file_ext' => 'File Ext',
            'mime_type' => 'Mime Type',
            'file_name' => 'File Name',
            'file_url' => 'File Url',
            'file_size' => 'File Size',
            'thumbnail_name' => 'Thumbnail Name',
            'thumbnail_url' => 'Thumbnail Url',
            'base_url' => 'Base Url',
            'has_remote' => 'Has Remote',
            'remote_domain' => 'Remote Domain',
            'remote_file_key' => 'Remote File Key',
            'display_order' => 'Display Order',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
