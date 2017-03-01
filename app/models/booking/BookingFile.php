<?php
namespace app\models\booking;
use Yii;
use app\models\EActiveRecord;

/**
 * This is the model class for table "booking_file".
 *
 * @property integer $id
 * @property integer $booking_id
 * @property integer $user_id
 * @property string $uid
 * @property string $file_ext
 * @property string $mime_type
 * @property string $file_name
 * @property string $file_url
 * @property integer $file_size
 * @property string $thumbnail_name
 * @property string $thumbnail_url
 * @property string $base_url
 * @property string $report_type
 * @property integer $has_remote
 * @property string $remote_domain
 * @property string $remote_file_key
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * @property Booking $booking
 */
class BookingFile extends EActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'booking_file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['booking_id', 'uid', 'file_ext', 'file_url'], 'required'],
            [['booking_id', 'user_id', 'file_size', 'has_remote', 'display_order'], 'integer'],
            [['date_created', 'date_updated', 'date_deleted'], 'safe'],
            [['uid'], 'string', 'max' => 32],
            [['file_ext', 'report_type'], 'string', 'max' => 10],
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
            'uid' => 'Uid',
            'booking_id' => 'Booking ID',
            'user_id' => 'User',
            'file_ext' => 'File Ext',
            'mime_type' => 'Mime Type',
            'file_name' => 'File Name',
            'file_url' => 'File Url',
            'file_size' => 'File Size',
            'thumbnail_name' => 'Thumbnail Name',
            'thumbnail_url' => 'Thumbnail Url',
            'display_order' => 'Display Order',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBooking()
    {
        return $this->hasOne(Booking::className(), ['id' => 'booking_id']);
    }
    public function initModel($bookingId, $userId, $file) {
        $this->setBookingId($bookingId);
        $this->setUserId($userId);
        $this->setFileAttributes($file);
    }

    public function saveModel() {
        if ($this->validate()) {    // validates model attributes before saving file.
            try {
                $fileSysDir = $this->getFileSystemUploadPath();
                createDirectory($fileSysDir);
                //Thumbnail.
                $thumbImage = Yii::app()->image->load($this->file->getTempName());
                // $image->resize(400, 100)->rotate(-45)->quality(75)->sharpen(20);
                $thumbImage->resize($this->thumbnail_width, $this->thumbnail_height);
                if ($thumbImage->save($fileSysDir . '/' . $this->getThumbnailName()) === false) {
                    throw new \Exception('Error saving file thumbnail.');
                }
                if ($this->file->saveAs($fileSysDir . '/' . $this->getFileName()) === false) {
                    throw new \Exception('Error saving file.');
                }

                // validation is done before hand, so skip validation when saving into db.
                return $this->save(false);
            } catch (\Exception $e) {
                $this->addError('file', $e->getMessage());
                return false;
            }
        } else {
            return false;
        }
    }

    //Overwrites parent::getFileUploadRootPath().
    public function getFileUploadRootPath() {
        return Yii::app()->params['bookingFilePath'];
    }

    public function getFileSystemUploadPath($folderName = null) {
        return parent::getFileSystemUploadPath($folderName);
    }

    public function getFileUploadPath($folderName = null) {
        return parent::getFileUploadPath($folderName);
    }

    public function deleteModel($absolute = true) {
        return parent::deleteModel($absolute);
    }

    /*     * ****** Query Methods ******* */

    public function getAllByBookingId($bid) {
        $criteria = new CDbCriteria(array('order' => 't.display_order'));
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->compare('booking_id', $bid);
        return $this->findAll($criteria);
    }

    /*     * ****** Accessors ****** */

  /*  public function getBooking() {
        return $this->booking;
    }*/

    public function getBookingId() {
        return $this->booking_id;
    }

    public function setBookingId($v) {
        $this->booking_id = $v;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($v) {
        $this->user_id = $v;
    }

    public function getHasRemote() {
        return $this->has_remote;
    }
}
