<?php
namespace app\models\user;
use Yii;
use app\components\StatCode;
use app\models\EActiveRecord;
/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $uid
 * @property string $username
 * @property integer $role
 * @property string $name
 * @property string $email
 * @property string $qq
 * @property string $wechat
 * @property string $password
 * @property string $user_key
 * @property integer $login_attempts
 * @property string $salt
 * @property string $password_raw
 * @property string $user_key_raw
 * @property integer $terms
 * @property string $date_activated
 * @property string $last_login_time
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * @property UserDoctorCert[] $userDoctorCerts
 * @property UserDoctorHuizhen[] $userDoctorHuizhens
 * @property UserDoctorProfile[] $userDoctorProfiles
 * @property UserDoctorZhuanzhen[] $userDoctorZhuanzhens
 */
class User extends EActiveRecord
{
    const ROLE_PATIENT = 1;
    const ROLE_DOCTOR = 2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'salt'], 'required'],
            [['role', 'login_attempts', 'terms'], 'integer'],
            [['date_activated', 'last_login_time', 'date_created', 'date_updated', 'date_deleted'], 'safe'],
            [['uid'], 'string', 'max' => 32],
            [['username'], 'string', 'max' => 11],
            [['name', 'qq', 'wechat'], 'string', 'max' => 45],
            [['email'], 'string', 'max' => 255],
            [['password', 'user_key'], 'string', 'max' => 64],
            [['salt', 'password_raw', 'user_key_raw'], 'string', 'max' => 40],
            [['username', 'role'], 'unique', 'targetAttribute' => ['username', 'role'], 'message' => 'The combination of Username and Role has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' =>  Yii::t('user', '手机号码'),
           // 'username' => Yii::t('user', '姓名'),
            'role' => Yii::t('user', '角色'),
            'name' =>  Yii::t('user', '姓名'),
            'email' => Yii::t('user', '邮箱'),
            'qq' => 'Qq',
            'wechat' => Yii::t('user', '微信'),
            'password' => Yii::t('user', '登录密码'),
           // 'user_key' => 'User Key',
            'login_attempts' => Yii::t('user', '登录尝试次数'),
            'salt' => 'Salt',
            'password_raw' =>  Yii::t('user', '登录密码'),
            //'user_key_raw' => 'User Key Raw',
          //  'terms' => 'Terms',
            'date_activated' => Yii::t('user', '激活日期'),
            'last_login_time' =>Yii::t('user', '最后登录时间'),
            'date_created' => Yii::t('site', '创建日期'),
            'date_updated' =>  Yii::t('site', '更新日期'),
            'date_deleted' => Yii::t('site', '删除日期'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserMedicalRecord()
    {
        return $this->hasMany(MedicalRecord::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserDoctorCerts()
    {
        return $this->hasMany(UserDoctorCerts::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserDoctorProfiles()
    {
        return $this->hasOne(UserDoctorProfile::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatientInfo()
    {
        return $this->hasMany(PatientInfo::className(), ['creator_id ' => 'id']);
    }


    //----------------------自定义方法-----------------------------
    public function beforeValidate(){
        parent::beforeValidate();
        if(empty($this->uid)){
            $this->createUID();
        }
        return true;
    }
    /*     * ****** Query Methods ******* */

    /**
     * @param string $username  User.username.
     * @return User model.
     */
    public function getByUsername($username) {
        return $this->getByAttributes(array('username' => $username));
    }

    public function getByUsernameAndRole($username, $role) {
        return $this->getByAttributes(array('username' => $username, 'role' => $role));
    }

    /*     * ****** Public Methods ****** */

    public function createNewModel() {
        //用户名
        $this->createSalt();
        //生成密码
        $this->createPassword();
    }
    //check登陆密码
    public function checkLoginPassword($passwordInput) {
        return ($this->password === $this->encryptPassword($passwordInput));
    }
    //更换密码
    public function changePassword($passwordInput) {
        $this->password_raw = $passwordInput;
        $this->password = $this->encryptPassword($passwordInput);
        return $this->update(array('password', 'password_raw'));
    }

    public function checkUsernameExists($username) {
        $criteria = new \yii\db\ActiveQuery();
        $criteria ->from('user');
        return $criteria->where(array('date_deleted'=>NULL,'username'=>$username))->exists();
       // return $this->exists('username=:username AND date_deleted is NULL', array(':username' => $username));
    }

    public function isDoctor($checkVerify = true) {
        if ($this->role != StatCode::USER_ROLE_DOCTOR) {
            return false;
        } elseif ($checkVerify) {
            $userDoctorProfile = $this->getUserDoctorProfile();
            return (isset($userDoctorProfile) && $userDoctorProfile->getDateVerified(false) !== null);
        } else {
            return true;
        }
    }

    /*     * ****** Private Methods ******* */
    private function createUID(){
        $this->uid = $this->strRandom(32);
    }

    private function createSalt() {
        $this->salt = $this->strRandom(40);
    }

    private function createPassword() {
        $this->setPassword($this->encryptPassword($this->password_raw));
    }

    public function encryptPassword($password, $salt = null) {
        if ($salt === null) {

            return ($this->encrypt($password . $this->salt));
        } else {
            return ($this->encrypt($password . $salt));
        }
    }

    private function encrypt($value) {
        return hash('sha256', $value);
    }

    // Max length supported is 62.
    private function strRandom($length = 40) {
        $chars = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        shuffle($chars);
        $ret = implode(array_slice($chars, 0, $length));
        return ($ret);
    }

    /*     * ****** Query Methods ******* */

    public function createCriteriaMedicalRecords() {
        $criteria = new \yii\db\ActiveQuery();
        $criteria ->from('user');
        $criteria->andWhere('user_id= '.$this->id);
        $criteria->orderby = 'date_created ASC';
        $criteria::with(array('mrBookings'));
       // $criteria->with = array('mrBookings');
        return $criteria;
    }

    /*     * ****** Accessors ******* */

  /* public function getUserDoctorCerts() {
        return $this->userDoctorCerts;
    }*/

    public function getUserDoctorProfile() {
        return $this->userDoctorProfile;
    }

    public function getUserMedicalRecords() {
        $this->getUserMedicalRecord()->with('Booking');
       // return $this->userMedicalRecords->with('mrBookings');
    }

    public function getUid() {
        return $this->uid;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($v) {
        $this->username = $v;
    }

    public function getMobile() {
        return $this->username;
    }

    public function getRole() {
        return $this->role;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getName() {
        return $this->name;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($v) {
        $this->password = $v;
    }

    public function setTerms($v) {
        $this->terms = $v;
    }

    public function isActivated() {
        return $this->date_activated !== null;
    }

    public function setActivated() {
        $this->date_activated = new \yii\db\Expression("NOW()");
    }

    public function getLastLoginTime() {
        return $this->last_login_time;
    }

    public function setLastLoginTime($v) {
        $this->last_login_time = $v;
    }

}
