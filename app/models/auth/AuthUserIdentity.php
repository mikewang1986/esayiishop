<?php
namespace app\models\auth;
use app\models\user\User;
use Yii;
use app\components\StatCode;
use app\apiservices\ErrorList;
/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class AuthUserIdentity  extends User {
    const ERROR_NONE=0;
    const ERROR_USERNAME_INVALID=1;
    const ERROR_PASSWORD_INVALID=2;
    const ERROR_UNKNOWN_IDENTITY=100;

    /**
     * @var integer the authentication error code. If there is an error, the error code will be non-zero.
     * Defaults to 100, meaning unknown identity. Calling {@link authenticate} will change this value.
     */

    const AUTH_TYPE_PASSWORD = 1; // authenticate by using password.
    const AUTH_TYPE_TOKEN = 2;    // authenticate by using token.
    public $auth_type;
    private $user;  // User model.
    private $token; // AuthTokenUser.
    private $role; //User role
    private $agent; //User agent
    public $errorCode=self::ERROR_UNKNOWN_IDENTITY;
    public function __construct($username, $password, $authType, $role=StatCode::USER_ROLE_PATIENT, $agent = NULL) {
        $this->username = $username;
        $this->password = $password;    // used as token is action_type is 'by token'.
        $this->auth_type = $authType;
        $this->role = $role;
        $this->agent = $agent;
    }
    /**
     * Returns a value indicating whether the identity is authenticated.
     * This method is required by {@link IUserIdentity}.
     * @return boolean whether the authentication is successful.
     */
    public function getIsAuthenticated()
    {
        return $this->errorCode==self::ERROR_NONE;
    }
    public function authenticate() {
        switch ($this->auth_type) {
            case self::AUTH_TYPE_PASSWORD:
                return $this->authenticatePassword();
            case self::AUTH_TYPE_TOKEN:
                return $this->authenticateToken();
            default:
                $this->errorCode = ErrorList::AUTH_UNKNOWN_TYPE;
                return false;
        }
    }

    public function authenticatePassword() {
        $userobject=new User;
        $this->user = $userobject->getByUsername($this->username);
        if ($this->user === null) {
            $this->errorCode = ErrorList::AUTH_USERNAME_INVALID;
        } else if ($this->user->checkLoginPassword($this->password) === false) {
            $this->errorCode = ErrorList::AUTH_PASSWORD_INVALID; //Wrong password.
        } else {
            //$this->id = $user->getId();
            if ($this->user->getLastLoginTime() === null) {
                $lastLogin = time();
            } else {
                $lastLogin = strtotime($this->user->getLastLoginTime());
            }
          //  $this->setState('lastLoginTime', $lastLogin); //* Can be accessed by Yii::app()->user->lastLoginTime;
            $now = new \yii\db\Expression("NOW()");
            $this->user->setLastLoginTime($now);
            $this->user->update('last_login_time');
            $this->errorCode = ErrorList::ERROR_NONE;
        }

        return !$this->errorCode;
    }

    /**
     * authenticates user by token and username.     
     */
    public function authenticateToken() {

        $tokenmodel=new AuthTokenUser;
        if($this->role == StatCode::USER_ROLE_PATIENT){
            $this->token = $tokenmodel->verifyTokenPatient($this->password, $this->username);
        }elseif($this->role == StatCode::USER_ROLE_DOCTOR){
            $this->token = $tokenmodel->verifyTokenDoctor($this->password, $this->username);
        }

        if (is_null($this->token) || $this->token->isTokenValid() === false) {
            $this->errorCode = ErrorList::AUTH_TOKEN_INVALID;
        } else {
            $this->errorCode = ErrorList::ERROR_NONE;
            $this->user = $this->token->getUser();
        }

        return $this->errorCode === ErrorList::ERROR_NONE;
    }

    public function hasSuccess() {
        return $this->errorCode === ErrorList::ERROR_NONE;
    }

    public function getUser() {

        return $this->user;
    }

    public function getToken() {
        return $this->token;
    }

}
