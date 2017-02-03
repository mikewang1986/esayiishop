<?php
namespace app\models\auth;
use Yii;
use app\models\EActiveRecord;
use app\models\user\User;
use app\components\StatCode;
/**
 * This is the model class for table "auth_token_user".
 *
 * @property integer $id
 * @property string $token
 * @property string $username
 * @property integer $user_role
 * @property integer $user_id
 * @property integer $is_active
 * @property integer $time_expiry
 * @property string $user_host_ip
 * @property string $user_mac_address
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 * @property string $user_agent
 *
 * @property User $user
 */
class AuthTokenUser extends EActiveRecord
{
    const EXPIRY_DEFAULT = 31536000;    //one year
    const WAP_EXPIRY_DEFAULT = 3600;   //a hour
    const ERROR_NONE = 0;
    const ERROR_NOT_FOUND = 1;
    const ERROR_INACTIVE = 2;
    const ERROR_EXPIRED = 3;
    public $error_code;
    private $verified = false;  // flag indicating if token is verified.
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_token_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_role', 'user_id', 'is_active', 'time_expiry'], 'integer'],
            [['date_created', 'date_updated', 'date_deleted'], 'safe'],
            [['token'], 'string', 'max' => 32],
            [['username'], 'string', 'max' => 11],
            [['user_host_ip'], 'string', 'max' => 15],
            [['user_mac_address'], 'string', 'max' => 50],
            [['user_agent'], 'string', 'max' => 20],
            [['user_id', 'token'], 'unique', 'targetAttribute' => ['user_id', 'token'], 'message' => 'The combination of Token and User ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'token' => 'Token',
            'username' => 'Username',
            'user_role' => 'User Role',
            'user_id' => 'User ID',
            'is_active' => 'Is Active',
            'time_expiry' => 'Time Expiry',
            'user_host_ip' => 'User Host Ip',
            'user_mac_address' => 'User Mac Address',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
            'user_agent' => 'User Agent',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function gethasUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    // 创建 token。
    public function createTokenPatient($userId, $username, $userHostIp, $userMacAddress) {
        return $this->createToken($userId, $username, StatCode::USER_ROLE_PATIENT, $userHostIp, $userMacAddress);
    }

    public function createTokenDoctor($userId, $username, $userHostIp, $userMacAddress) {
        return $this->createToken($userId, $username, StatCode::USER_ROLE_DOCTOR, $userHostIp, $userMacAddress);
    }

    // 创建 token -WAP患者端
    public function createWapTokenPatient($userId, $username, $userHostIp, $userMacAddress)
    {
        return $this->createWapToken($userId, $username, StatCode::USER_ROLE_PATIENT, $userHostIp, $userMacAddress);
    }

    public function createToken($userId, $username, $userRole, $userHostIp, $userMacAddress) {
        $this->setUserId($userId);
        $this->setUsername($username);
        $this->setUserRole($userRole);
        $this->setToken();
        $this->setTimeExpiry();
        $this->setUserHostIp($userHostIp);
        $this->setUserMacAddress($userMacAddress);
        $this->setIsActive(true);
    }

    public function createWapToken($userId, $username, $userRole, $userHostIp, $userMacAddress,$user_agent = 'wap')
    {
        $this->setUserId($userId);
        $this->setUsername($username);
        $this->setUserRole($userRole);
        $this->setToken();
        $this->setUserAgent($user_agent);
        $this->setWapTimeExpiry();
        $this->setUserHostIp($userHostIp);
        $this->setUserMacAddress($userMacAddress);
        $this->setIsActive(true);
    }


    // 验证 token。
    public function verifyTokenPatient($token, $username) {
        return $this->verifyByTokenAndUsernameAndRole($token, $username, StatCode::USER_ROLE_PATIENT);
    }

    public function verifyTokenDoctor($token, $username) {
        return $this->verifyByTokenAndUsernameAndRole($token, $username, StatCode::USER_ROLE_DOCTOR);
    }

    public function verifyByTokenAndUsernameAndRole($token, $username, $userRole) {
        $model = $this->getByTokenAndUsernameAndRole($token, $username, $userRole, true);
        if (isset($model)) {
            $model->verifyToken();
            return $model;
        } else {
            return null;
        }
    }

    //@不用了。 - 2015-10-28 by QP
    public function verifyByTokenAndUsername($token, $username) {
        $model = $this->getByTokenAndUsername($token, $username, true);
        if (isset($model)) {
            $model->verifyToken();
            return $model;
        } else {
            return null;
        }
    }

    public function verifyToken() {
        if ($this->checkExpiry()) {
            $this->error_code = self::ERROR_NONE;
        } else {
            $this->error_code = self::ERROR_EXPIRED;
        }
        $this->verified = true;
    }

    public function isTokenValid() {
        return ($this->verified && $this->error_code === self::ERROR_NONE);
    }

    /*
      public function verifyToken($token, $username) {
      $tokenUser = $this->getByTokenAndUsername($token, $username, true);
      if (isset($tokenUser)) {
      if ($this->checkExpiry($tokenUser)) {
      $this->error_code = self::ERROR_NONE;
      } else {
      $this->error_code = self::ERROR_EXPIRED;
      }
      } else {
      $this->error_code = self::ERROR_NOT_FOUND;
      }
      return $this->error_code == self::ERROR_NONE;
      }
     *
     */

    public function deActivateToken() {
        $this->setIsActive(false);
        $this->date_updated = new \yii\db\Expression("NOW()");
        return $this->update(array('is_active', 'date_updated'));
    }

    public function deActivateAllOldTokens() {
        $now = new \yii\db\Expression("NOW()");
        return $this->updateAllByAttributes(array('is_active' => 0, 'date_updated' => $now), array('user_id' => $this->user_id, 'is_active' => '1'));
    }

    public function checkExpiry() {
        if (is_null($this->time_expiry)) {
            return true;
        } else {
            $now = time();
            return ($this->time_expiry > $now);
        }
    }

    /*     * ****** private methods  ******* */

    // Max length supported is 62.
    private function strRandom($length = 40) {
        $chars = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        shuffle($chars);
        $ret = implode(array_slice($chars, 0, $length));

        return ($ret);
    }

    private function getByTokenAndUsernameAndRole($token, $username, $userRole, $isActiveFlag = true) {
        $isActive = $isActiveFlag === true ? 1 : 0;
        $model = $this->getByAttributes(array('token' => $token, 'username' => $username, 'user_role' => $userRole, 'is_active' => $isActive));
        if (isset($model)) {
            return $model;
        }
        return null;
    }

    //@不用了。 参照 getByTokenAndUsernameAndRole(). - 2015-10-28 by QP
    private function getByTokenAndUsername($token, $username, $isActiveFlag = true) {
        $isActive = $isActiveFlag === true ? 1 : 0;
        $model = $this->getByAttributes(array('token' => $token, 'username' => $username, 'is_active' => $isActive));
        if (isset($model)) {
            return $model;
        }
        return null;
    }

    // 得到当前token信息 add by wanglei 2016-09-13
    public function getAllByToken($token,$with=NULL)
    {
        $model = $this->getByAttributes(array(
            'token' => $token,
        ),$with);
        if (isset($model)) {
            return $model;
        }

        return null;
    }

    /*     * ****** Query Methods ******* */

    public function getAllActiveByUserId($userId) {
        $now = time();
        $criteria = new CDbCriteria();
        $criteria->addCondition("t.date_deleted is NULL");
        $criteria->compare("user_id", $userId);
        $criteria->compare("is_active", '1');
        $criteria->compare('time_expiry', ">:" . $now);
        return $this->findAll($criteria);
    }

    public function getFirstActiveByUserId($userId) {
        $criteria = new CDbCriteria();
        $criteria->addCondition("t.date_deleted is NULL");
        $criteria->compare("t.user_id", $userId);
        $criteria->compare("t.is_active", '1');
        return $this->find($criteria);
    }

    /*     * ****** Accessors ******* */

    public function getUser() {
        return $this->gethasUser()->one();
    }

    public function getToken() {
        return $this->token;
    }

    private function setToken() {
        $this->token = strtoupper(substr(str_shuffle(MD5(microtime())), 0, 32));   // refer to helper.php
    }

    public function getUserId() {
        return $this->user_id;
    }

    private function setUserId($v) {
        $this->user_id = $v;
    }

    public function getUsername() {
        return $this->username;
    }

    private function setUsername($v) {
        $this->username = $v;
    }

    public function getTimeExpiry() {
        return $this->time_expiry;
    }

    private function setTimeExpiry() {
        $duration = self::EXPIRY_DEFAULT;
        $now = time();
        $this->time_expiry = $now + $duration;
    }

    private function setWapTimeExpiry()
    {
        $duration = self::WAP_EXPIRY_DEFAULT;
        $now = time();
        $this->time_expiry = $now + $duration;
    }

    public function getUserHostIp() {
        return $this->user_host_ip;
    }

    private function setUserHostIp($v) {
        $this->user_host_ip = $v;
    }

    public function getUserMacAddress() {
        return $this->user_mac_address;
    }

    private function setUserMacAddress($v) {
        $this->user_mac_address = $v;
    }

    private function setIsActive($v) {
        $this->is_active = $v === true ? 1 : 0;
    }

    private function setUserRole($v) {
        $this->user_role = $v;
    }

    private function setUserAgent($v){
        $this->user_agent = $v;
    }
    //更新续登陆时间
    public function durationTokenPatient($token, $username)
    {
        $now = new \yii\db\Expression("NOW()");
        return $this->updateAllByAttributes(array(
            'time_expiry' => time()+self::WAP_EXPIRY_DEFAULT,
            'date_updated' => $now
        ), array(
            'token' => $token,
            'username' => $username,
            'user_role' => StatCode::USER_ROLE_PATIENT
        ));
    }

}
