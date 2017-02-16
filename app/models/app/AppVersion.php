<?php
namespace app\models\app;
use Yii;
use app\models\EActiveRecord;
/**
 * This is the model class for table "app_version".
 *
 * @property integer $id
 * @property string $app_name
 * @property string $os
 * @property string $os_version
 * @property string $device
 * @property integer $app_version
 * @property string $app_version_name
 * @property integer $is_force_update
 * @property string $date_active
 * @property string $change_log
 * @property string $app_dl_url
 * @property string $app_qq_url
 * @property string $app_wandoujia_url
 * @property string $app_baidu_url
 * @property string $app_360
 * @property string $app_xiaomi
 * @property string $remark
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class AppVersion extends EActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_version';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['os', 'os_version', 'app_version', 'date_active'], 'required'],
            [['app_version', 'is_force_update'], 'integer'],
            [['date_active', 'date_created', 'date_updated', 'date_deleted'], 'safe'],
            [['change_log'], 'string'],
            [['app_name', 'app_version_name'], 'string', 'max' => 20],
            [['os', 'os_version', 'device'], 'string', 'max' => 45],
            [['app_dl_url', 'app_qq_url', 'app_wandoujia_url', 'app_baidu_url', 'app_360', 'app_xiaomi'], 'string', 'max' => 200],
            [['remark'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'os' => 'Os',
            'os_version' => 'Os Version',
            'device' => 'Device',
            'app_version' => 'App Version',
            'app_dl_url' => 'App Dl Url',
            'is_force_update' => 'Is Force Update',
            'change_log' => 'Change Log',
            'date_active' => 'Date Active',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        ];
    }
    /*     * ****** Query Methods ******* */

    public function getLastestVersionByOSAndAppName($os, $app_name) {
        // TODO: add 'date_active'. $now > $date_active.
        $criteria = new \yii\db\ActiveQuery('app\models\app\AppVersion');
        $criteria->andWhere('date_deleted is NULL');
        $criteria->andWhere('app_name="'. $app_name.'"');
        $criteria->andWhere('os="'. $os.'"');
        $criteria->addOrderBy('app_version DESC');
        $criteria->limit = 1;
        $ret = $criteria->all();
        $model = array_shift($ret);
        return $model;
    }

    public function getLatestActiveVersionByOS($os) {
        // TODO: add 'date_active'. $now > $date_active.
        $now = new \yii\db\Expression("NOW()");
        $criteria = new \yii\db\ActiveQuery('app\models\app\AppVersion');
        $criteria->andWhere('date_deleted is NULL');
        $criteria->andWhere('os="'. $os.'"');
        $criteria->andWhere('date_active<='.$now);
        $criteria->order = 'app_version DESC';
        $criteria->limit = 1;
        $ret = $criteria->all();
        $model = array_shift($ret);
        return $model;
    }

    /*     * ****** Accessors ******* */

    public function getOS() {
        return $this->os;
    }

    public function getOSVersion() {
        return $this->os_version;
    }

    public function getDevice() {
        return $this->device;
    }

    public function getAppVersion() {
        return $this->app_version;
    }

    public function getAppDownloadUrl() {
        return $this->app_dl_url;
    }

    public function getIsForceUpdate() {
        if ($this->is_force_update == 1) {
            return "1";
        } else {
            return "0";
        }
    }

    public function getChangeLog() {
        return $this->change_log;
    }

    public function getDateActive() {
        return $this->date_active;
    }

}
