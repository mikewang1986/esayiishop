<?php
namespace app\models\app;
use Yii\mongodb\ActiveRecord;
/**
 * This is the model class for table "patient_stat_log".
 *
 * The followings are the available columns in table 'patient_stat_log':
 * @property integer $id
 * @property string $user_host_ip
 * @property string $url
 * @property integer $site
 * @property string $key_word
 * @property string $url_referrer
 * @property string $user_agent
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class PatientStatLogMongo extends ActiveRecord
{
        public $id;
        public $user_host_ip;
        public $url;
        public $site;
        public $key_word;
        public $url_referrer;
        public $user_agent;
        public $date_created;
        public $date_updated;
        public $date_deleted;
     // This method is required!
	public static function collectionName()
	{
		return 'patient_stat_log';
	}
	public function saveInfo($value)
	{
		$customer = new PatientStatLogMongo ();
		$customer->user_host_ip = $value['user_host_ip'];
		$customer->site = $value['site'];
		$customer->key_word= $value['key_word'];
		$customer->insert ();
		return $customer;
	}
	public function attributes()
	{
		return [
				'_id',
				'user_host_ip',
				'url',
				'site',
				'key_word',
				'url_referrer',
				'user_agent',
				'date_created',
				'date_updated',
				'date_deleted',
		];
	}

}
