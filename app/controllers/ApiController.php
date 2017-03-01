<?php
namespace app\controllers;
use Yii;
//定义的共通
use app\apiservices\EApiViewService;
use app\apiservices\ErrorList;
use app\apiservices\ApiViewPatientLocalData;
use app\models\UserManager;
use app\models\User;
use app\models\AuthManager;
use app\apiservices\ApiViewBookingListV5;
use yii\helpers\BaseJson;
use app\apiservices\ApiViewSuccessCase;
use app\apiservices\ApiViewExpertsShow;
use app\apiservices\v12\ApiViewEvaluationListV12;
use app\models\OperationManager;
use app\models\FeedbackManager;
use app\models\PaymentManager;
use app\models\booking\BookingServiceConfig;
use app\models\StatManager;
use app\components\ErrorCode;
use app\models\AppManager;
use app\apiservices\api\ApiViewDiseaseCategory;
use app\apiservices\api\ApiViewSubCategory;
use app\models\BookingManager;
use app\apiservices\api\ApiViewTopHospital;
class ApiwapController extends \yii\web\Controller
{
    // Members
    /**
     * Key which has to be in HTTP USERNAME and PASSWORD headers
     */
    Const APPLICATION_ID = 'ASCCPE';
    /**
     * Default response format
     * either 'json' or 'xml'
     */
    private $format = 'json';
    /**
     *
     * @return array action filters
     */
    public function filters()
    {
        return array();
    }
  //加入域名判断
    public function domainWhiteList() {
         return Yii::$app->params['wapurl'];
    }  
    public function getHttpOrigin(){
        return isset($_SERVER['HTTP_ORIGIN'])? $_SERVER['HTTP_ORIGIN'] : '';     
    }
    
    public function setHeaderSafeDomain($whiteList, $domain=null){
        if(is_null($domain)){
            $domain = $this->getHttpOrigin();
        }
        $domain = strtolower($domain);
        if(arrayNotEmpty($whiteList)){
            if(in_array($domain, $whiteList)){
                header('Access-Control-Allow-Origin:'.$domain);  
            }
        }
    }
    public function init()
    {
        $domainWhiteList = $this->domainWhiteList();
        $this->setHeaderSafeDomain($domainWhiteList, null);
        header('Access-Control-Allow-Headers: Origin,X-Requested-With,Authorization,Accept,Content-Type');
        header('Access-Control-Max-Age:' , 3600 * 24);    
        //加入put可以使用
        header("Access-Control-Allow-Methods:DELETE,GET,HEAD,POST,PUT,OPTIONS");
        header('Access-Control-Allow-Credentials:true'); // 允许携带 用户认证凭据（也就是允许客户端发送的请求携带Cookie）
        $this->getmethod();
       // return parent::init();
    }
    //options返回200
    public function getmethod(){
        $method=$_SERVER['REQUEST_METHOD'];
        switch($method){
            case "OPTIONS":
               Yii::$app->end();
            break;    
        }
    }
    // Actions
    public function actionList($model)
    {
        $api = $this->getApiVersionFromRequest();
        // Get the respective model instance
        switch ($model) {
            case 'dataversion'://数据版本号
                $output= new \stdClass();
                $output->errorCode=ErrorCode::ERROR_NO;
                $output->errorMsg=ErrorCode::getErrText(ErrorCode::ERROR_NO);
                $output->results=array(
                    'version' => '20151215',
                    'localdataUrl' => Yii::$app->urlManager->createUrl('/api/localdata'),
                );
            break;
            case 'localdata'://本地需要缓存的数据
                $apiService = new ApiViewPatientLocalData();
                $output = $apiService->loadApiViewData();
            break;

           /* case 'faculty'://科室
                $facultyMgr = new FacultyManager();
                $output = $facultyMgr->loadFacultyList();
                break;
            case 'overseas'://医院
                $overseasMgr = new OverseasManager();
                $output = $overseasMgr->loadHospitalsJson();
                break;*/
            case "wechatinit":
                $wechat = Yii::$app->wechat;
                $wechat->valid();
                exit;
             break;
            case 'getrcode':
                $wechat = Yii::$app->wechat;
                $qrcode = $wechat->createQrCode([
                    'expire_seconds' => 604800,
                    'action_name' => 'QR_SCENE',
                    'action_info' => ['scene' => ['scene_id' => rand(1, 999999999)]]
                ]);
                //获取二维码
                $imgRawData = $wechat->getQrCodeUrl($qrcode['ticket']);
                var_dump($imgRawData);
                exit;
             break;
            //得到关注者列表
            case 'getmemberlist':
                $wechat = Yii::$app->wechat;
                var_dump($wechat->getMemberList());
                exit;
                break;
            case 'getmemberinfo':
                $wechat = Yii::$app->wechat;
                $openid="oz9Tbjr82AUzuoH99SaqOp7nAt9g";
                var_dump($wechat->getMemberInfo($openid));
                exit;
             break;
            case 'appversion'://android ,ios 版本号 参数  
                $get = $_GET;
                $appMgr = new AppManager();
                $output = $appMgr->loadAppVersionJson($get);
            break;
            case "appnav3": //合作医院
                $values = $_GET;
                $apiService = new ApiViewAppNav3V4($values);
                $output = $apiService->loadApiViewData();
            break;
            case "faculty2":    // can be deleted, use appnav1 instead.
                $facultyMgr = new FacultyManager();
                $output = $facultyMgr->loadFacultyList2();
            break;
            //---------------------前端接口---------------
            case "hospital":
                $values = $_GET;
                if ($api >= 1){
                    $apiService = new ApiViewHospitalWapSearch($values);
                    $output = $apiService->loadApiViewData();
                }else{
                    $values['isNotPaging'] = 1;
                    $apiService = new ApiViewHospitalSearchV2($values);
                    $output = $apiService->loadApiViewData();
                    //格式化接口
                    $output=$this->loadformathospital($output);
                }
            break;
            //找医院
            case "findhospital":
                $values = $_GET;
                $apiService = new ApiViewHospitalSearchV7($values);
                $output = $apiService->loadApiViewData();
            break;
            case "listhospital":
                $values = $_GET;
                $hospitalMgr = new HospitalManager();
                $query['city'] = isset($values['city']) ? $values['city'] : null;
                $output['hospitals'] = $hospitalMgr->loadListHospital($query, array('order' => 't.name'));
                break;
            case "tophospital"://医院排行榜
                $values=$_GET;
                $apiService = new ApiViewTopHospital($values);
                $output = $apiService->loadApiViewData();
                break;
            case 'doctor'://医生
                $values=$_GET;
                $apiService = new ApiViewDoctorSearchV7($values);
                $output = $apiService->loadApiViewData();
            break;
            case "userbooking"://
                $values = $_GET;
                $values['token'] = $this->em_getallheaders();
               // $values['token']='EC8332DE96455458DF4F0D25CB725386';
                $user = $this->userLoginRequired($values);
                if($user){
                    $apiService = new ApiViewBookingListV5($user,$values['bk_status'],true);
                    $output = $apiService->loadApiViewData();
                }
            break;
            case "userinfo"://用户详细界面
                $values = $_GET;
                $values['token'] = $this->em_getallheaders();
                $user = $this->userLoginRequired($values);
                if($user){
                    $apiService = new ApiViewUserinfo($user);
                    $output = $apiService->loadApiViewData();
                }
            break;
            case 'expertteam':
                $values = $_GET;
                $query['city'] = isset($values['city']) ? $values['city'] : null;
                $options = $this->parseQueryOptions($values);
                $with = array('expteamLeader');
                $expteamMgr = new ExpertTeamManager();
                $output['expertTeams'] = $expteamMgr->loadAllIExpertTeams($query, $with, $options);
            break;
            case 'disease':
                $diseaseMgr = new DiseaseManager();
                $output = $diseaseMgr->loadListDisease();
                break;
            case 'diseasename'://根据疾病名称获取疾病信息
                $values = $_GET;
                $apiService = new ApiViewDiseaseName($values);
                $output = $apiService->loadApiViewData();
                break;
            case 'city':
                $values = $_GET;
                $values['type']='doctor';
                $city = new ApiViewOpenCity($values);
                $output = $city->loadApiViewData();
                break;
            case 'diseasecategory'://获取疾病分类
                $apiService = new ApiViewDiseaseCategory();
                $output = $apiService->loadApiViewData();
                break;
            case 'recommendeddoctors'://首页推荐的医生
                $apiService = new ApiViewRecommendedDoctors();
                $output = $apiService->loadApiViewData();
                break;
            case 'commonwealdoctors'://名医公益推荐的医生
                $apiService = new ApiViewCommonwealDoctors();
                $output = $apiService->loadApiViewData();
                break;
            case 'diagnosisdoctors'://名医公益推荐的医生
                $values = $_GET;
                $apiService = new ApiViewDiagnosisDoctor($values);
                $output = $apiService->loadApiViewData();
                break;
            case 'search':
                $values = $_GET;
                $apiService = new ApiViewSearch($values);
                $output = $apiService->loadApiViewData();
            break;
            //验证码
            case 'getcaptcha';
                $values = $_GET;
                $captcha = new CaptchaManage(125,26,6,"wap");
                $resultimage=$captcha->showImg();
                $auth_captcha=new AuthCaptchaManage();
                $output = $auth_captcha->createCaptcha($resultimage); 
            break;
            //验证验证码
            case 'checkcaptcha':
                $values = $_GET;
                $auth_captcha=new AuthCaptchaManage();
                $output = $auth_captcha->checkCaptcha($values); 
            break;
            case 'area':
                $apiService = new ApiViewArea();
                $output = $apiService->loadApiViewData();
                break;
            //成功案例
            case "successfulcase":
                $values = $_GET;
                $apiService = new ApiViewSuccessCase($values);
                $output = $apiService->loadApiViewData();
                break;
            //专家展示
            case "expertsshow":
                $values = $_GET;
                $apiService = new ApiViewExpertsShow($values);
                $output = $apiService->loadApiViewData();
                break;
            default:
                // Model not implemented error
                // $this->_sendResponse(501, sprintf('Error: Mode <b>list</b> is not implemented for model <b>%s</b>', $model));
                $this->_sendResponse(501, sprintf('Error: Invalid request', $model));
                Yii::$app->end();
        }
        // Did we get some results?
        if (empty($output)) {
            // No
            // $this->_sendResponse(200, sprintf('No items where found for model <b>%s</b>', $model));
            $this->_sendResponse(200, sprintf('No result', $model));
        } else {

            $this->renderJsonOutput($output);
            // header('Content-Type: text/html; charset=utf-8');
            // var_dump($output);
        }
    }

    public function actionView($model, $id)
    {
      ;
        // Check if id was submitted via GET
        if (isset($id) === false) {
            $this->_sendResponse(500, 'Error: Parameter <b>id</b> is missing');
        }
        $output = null;
        switch ($model) {
             // Find respective model    
            case 'faculty':  //TODO: this api is used in v1. will not be supported after v2.0.
                $facultyMgr = new FacultyManager();
                $output = $facultyMgr->loadIFacultyJson($id);
                break;
            case 'hospital':
                $apiService = new ApiViewHospitalV4($id);
                $output = $apiService->loadApiViewData();
                break;
            case 'doctor':
                $apiService = new ApiViewDoctorV14($id);
                $output = $apiService->loadApiViewData();
                break;

            // app v2.0 api.            
            case 'faculty2':
                $facultyMgr = new FacultyManager();
                $ifaculty = $facultyMgr->loadIFaculty2($id);
                $faculty = new stdClass();
                $faculty->id = $ifaculty->id;
                $faculty->code = $ifaculty->code;
                $faculty->name = $ifaculty->name;
                $faculty->desc = $ifaculty->desc;
                $output['faculty'] = $faculty;
                $output['diseases'] = $ifaculty->diseases;
                $output['expertTeams'] = $ifaculty->expertTeams;
                $output['doctors'] = $ifaculty->doctors;
                break;
            case 'expertteam':
                $apiService = new ApiViewExpertTeamV5($id);
                $output = $apiService->loadApiViewData();
                break;
            case 'userbooking':
                $values = $_GET;
                $values['token'] = $this->em_getallheaders();
                $user = $this->userLoginRequired($values,true);
                $apiService = new ApiViewBookingV12($user, $id);
                $output = $apiService->loadApiViewData();
                //$user = $this->userLoginRequired($values);
                //$apiService = new ApiViewBookingV4($user, $id);
               //$output = $apiService->loadApiViewData();
                break;
            case 'hospitaldept':
                $searchInputs = $_GET;
                $apiService = new ApiViewHospitalDeptV11($id, $searchInputs);
                $output = $apiService->loadApiViewData();   
                break;
            case'disease':
                $apiSvc = new ApiViewDiseaseV4($id);
                $output = $apiSvc->loadApiViewData();
                break;
            case 'diseasebycategory'://根据疾病分类获取疾病
                $apiService = new ApiViewDiseaseByCategory($id);
                $output = $apiService->loadApiViewData();
                break;
            case 'subcategory':
                $apiService = new ApiViewSubCategory($id);
                $output = $apiService->loadApiViewData();
                break;
            case 'city':
                $apiService = new ApiViewCity($id);
                $output = $apiService->loadApiViewData();
                break;
            //获得预约评价
            case 'getevaluation':
                $values['token'] = $this->em_getallheaders();
                $user = $this->userLoginRequired($values,true);
                $user_id=$user->getId();
                $apiService = new ApiViewEvaluationListV12($user_id,$id);
                $output = $apiService->loadApiViewData();
            break;
            //发送指定邮件
            case 'getrpc':
                $model = new OperationManager();
                $output = $model->rpc_call($id);
            break;

            default:
                $this->_sendResponse(501, sprintf('Mode <b>view</b>  is not implemented for model <b>%s</b>', $model));
                Yii::$app->end();
        }
        // Did we find the requested model? If not, raise an error
        if (is_null($output)) {
            $this->_sendResponse(404, 'No result');
        } else {
            $this->renderJsonOutput($output);
        }
    }

    public function actionCreate($model)
    {

        $get = $_GET;
        $post = $_POST;

        if (empty($_POST)) {
            // application/json
            $post = BaseJson::decode($this->getPostData());
            
        } else {
            // application/x-www-form-urlencoded
            $post = $_POST;
        }
       
        // $api = $this->getApiVersionFromRequest();
        // if ($api >= 4) {
        // $output = array('status' => EApiViewService::RESPONSE_NO, 'errorCode' => ErrorList::BAD_REQUEST, 'errorMsg' => 'Invalid request.');
        // } else {
        // $output = array('status' => false, 'error' => 'Invalid request.');
        // }
        switch ($get['model']) {
            // Get an instance of the respective model
            //手机用户注册
            case 'userregister': // remote user register.
                if (isset($post['userRegister'])) {
                    $values = $post['userRegister'];
                    $values['userHostIp'] = Yii::$app->request->userHost;
                    $userMgr = new UserManager();
                    $output = $userMgr->apiTokenUserRegister($values);
                } else {
                    $output['errorCode'] = ErrorCode::ERROR_WRONG_PARAMETERS;
                    $output['errorMsg'] = ErrorCode::getErrText(ErrorCode::ERROR_WRONG_PARAMETERS);
                }
             break;
            // 手机密码登录
            case 'userlogin': // remote user login.
                if (isset($post['userLogin'])) {
                    // get user ip from request.
                    $values = $post['userLogin'];
                    $values['userHostIp'] = Yii::$app->request->userHost;
                    $values['agent_parmas'] = "wap";
                    $authMgr = new AuthManager();
                    $output = $authMgr->apiTokenUserLoginByPassword($values);
                } else {
                    $output['errorCode'] =ErrorCode::ERROR_WRONG_PARAMETERS;
                    $output['errorMsg'] =ErrorCode::getErrText(ErrorCode::ERROR_WRONG_PARAMETERS);
                }
            break;
            case 'usermobilelogin'://手机号和验证码登录
                if (isset($post['userLogin'])) {
                    // get user ip from request.
                    $values = $post['userLogin'];
                    $values['userHostIp'] = Yii::$app->request->userHost;
                    $authMgr = new AuthManager();
                    $output = $authMgr->apiTokenUserLoginByMobile($values);
                } else {
                    $output['errorCode'] =ErrorCode::ERROR_WRONG_PARAMETERS;
                    $output['errorMsg'] =ErrorCode::getErrText(ErrorCode::ERROR_WRONG_PARAMETERS);
                }
            break;
            case 'userresetpassword':    // rsest user password.
                if (isset($post['userReset'])) {
                    $values = $post['userReset'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $userMgr = new UserManager();
                    $output = $userMgr->apiTokenUserReset($values);
                } else {
                    $output['errorMsg'] = 'Wrong parameters.';
                }
            break;
            case 'quickbooking': // 快速预约
                if (isset($post['booking'])) {  
                    $values = $post['booking'];
                    $values['token'] = $this->em_getallheaders();
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $values['user_agent'] = ($this->isUserAgentWeixin()) ? StatCode::USER_AGENT_WEIXIN : StatCode::USER_AGENT_MOBILEWEB;
                    if (! isset($values['verify_code'])) {   
                        $checkVerifyCode = false;
                        $user = $this->userLoginRequired($values); // check if user has login.
                        if (is_object($user)) {
                            $values['user_id'] = $user->getId();
                            $values['mobile'] = $user->getUserName();
                        }
                    } else {
                        $checkVerifyCode = true;
                    }
                    $bookingMgr = new BookingManager();
                    $output = $bookingMgr->apiCreateQuickBooking($values, $checkVerifyCode);
                } else {
                    $output['error'] = 'missing parameters';
                }
             break;
            case 'quickbookingwifi':// 快速预约WIFI
                if (isset($post['booking'])) {
                    $values = $post['booking'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $values['user_agent'] = ($this->isUserAgentIOS()) ? StatCode::USER_AGENT_APP_IOS : StatCode::USER_AGENT_APP_ANDROID;
                    if(!isset($values['verify_code'])){
                        $checkVerifyCode = false;
                        $this->renderJsonOutput(array('status' => EApiViewService::RESPONSE_NO, 'errorCode' => ErrorList::BAD_REQUEST, 'errorMsg' => '没有输入验证码'));
                    }else{
                        $checkVerifyCode = true;
                    }
                    $bookingMgr = new BookingManager();
                    $output = $bookingMgr->apiCreateNewQuick($values, $checkVerifyCode);
                } else {
                    $output['error'] = 'missing parameters';
                }
            break;
            case 'smsverifycode': // sends sms verify code AuthSmsVerify.
                if (isset($post['smsVerifyCode'])) {
                    $values = $post['smsVerifyCode'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $authMgr = new AuthManager();
                    $output = $authMgr->apiSendVerifyCode($values);
                } else {
                    $output['error'] = 'Wrong parameters.';
                }
                break;
            case 'filetoken':
                $qiniuMgr = new QiniuManager();
                $url = $qiniuMgr->apiBookingToken();
                $data = $this->send_get($url);
                $output = array('remoteDomain'=>Yii::app()->params['qiniuyunurl'],'uploadToken' => $data['results']['uploadToken']);
                break; 
            case 'bookingfile':
                if (isset($post['bookingfile'])) {
                    $values = $post['bookingfile'];
                    $qiniuMgr = new QiniuManager();
                    $output = $qiniuMgr->apiBookingFile($values);
                } else {
                    $output['error'] = 'Wrong parameters.';
                }
                break;
             case 'booking':
                if (isset($post['booking'])) {
                    $values = $post['booking'];
                    $values['token'] = $this->em_getallheaders();
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $values['user_agent'] = ($this->isUserAgentIOS()) ? StatCode::USER_AGENT_APP_IOS : StatCode::USER_AGENT_APP_ANDROID;
                    $user = $this->userLoginRequired($values); // check if user has login.
                    if (is_object($user)) {
                        $values['user_id'] = $user->getId();
                        $values['mobile'] = $user->getUserName();
                    }
                    $checkVerifyCode = true;
                    $bookingMgr = new BookingManager();
                   // $checkVerifyCode = true;    // checks verify_code before creating a new booking in db.
                    $sendEmail = false;  // send email to admin after booking is created.
                    $output = $bookingMgr->apiCreateBookingV9($user, $values, $checkVerifyCode, $sendEmail);
                    } else {
                        $output['errorMsg'] = 'Wrong parameters.';
                    }
           
            break;    
            //加入统计
            case 'stat':
                if(isset($post['stat'])){
                    $values = $post['stat'];
                    $values['user_host_ip'] = Yii::$app->request->getUserIP();
                    $values['url'] = Yii::$app->request->getUrl();
                    $values['url_referrer'] = Yii::$app->request->getReferrer();
                    $values['user_agent'] = Yii::$app->request->userAgent;
                    $model = new StatManager();
                    $output = $model->createPatientStat($values);
                }
            break;
            //应用程序统计
            case 'applogstat':
                if(isset($post['applogstat'])){
                    $values = $post['applogstat'];
                    $model = new StatManager();
                    $output = $model->createAppLogStat($values);
                }
                break;
            case 'usertoken'://个人信息（基本信息）
                if (isset($post['usertoken'])) {
                    $values = $post['usertoken'];
                    $values['token'] = $this->em_getallheaders();
                    $authMgr = new AuthManager();
                    $values['userHostIp'] = Yii::$app->request->userHost;
                    $output = $authMgr->getTokenUserById($values);
                } else {
                    $output['errorMsg'] = 'Wrong parameters.';
                }
             break;
             //支付内容
            case 'payping'://支付信息
                $post = json_decode(file_get_contents('php://input'), true);
                if (isset($post)) {
                    //$values['token'] = $this->em_getallheaders();
                    //$user = $this->userLoginRequired($values);
                    $pageMgr = new PaymentManager();
                    $pageMgr->payping($post);
                }  
                else{
                    $output['errorMsg'] = 'Wrong parameters.';
                }
            break;   
            case 'paysuccess'://支付成功
                 if (isset($post['order'])) {
                    $values['token'] = $this->em_getallheaders();
                    $user = $this->userLoginRequired($values);
                    $value=$post['order'];
                    $pageMgr = new PaymentManager();
                    $output =$pageMgr->PayDeposit($value);
                } else {
                    $output['errorMsg'] = 'Wrong parameters.';
                }
            break;
            case 'evaluation'://个人信息（基本信息）
                if (isset($post['evaluation'])) {
                    $values = $post['evaluation'];
                    $values['token'] = $this->em_getallheaders();
                    $user = $this->userLoginRequired($values);  // check if user has login.
                    $evaluationMgr = new EvaluationManager();
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $output = $evaluationMgr->apiCreatePatientEvaluation($user, $values);
                } else {
                    $output['errorMsg'] = 'Wrong parameters.';
                }
             break;
            case   'cancelFileAll':

                    if (isset($post['file'])) {
                        $values['token'] = $this->em_getallheaders();
                      //  $values['token']="D16DB100F05E58FF5CAE6DDF8077E96F";;
                        $user = $this->userLoginRequired($values);
                        $userId=$user->getId();
                        $bookMgr = new BookingManager();

                        $output = $bookMgr->cancelallfile($post['file'],$userId);
                    }
                    else {
                        $output['errorMsg'] = 'Wrong parameters.';
                    }
             break;
            //免费咨询
            case 'feedbackwifi':
                if(isset($post['wifi'])){
                    $values = $post['wifi'];
                    $model = new FeedbackManager();
                    $output = $model->createfeedback($values);
                }
            break;
            //信息预约提交
            case 'admissionoperation':
                $values['token'] = $this->em_getallheaders();
                if(isset($post['booking'])){
                    $values = $post['booking'];
                    $model = new OperationManager();
                    $output = $model->createinformation($values);
                }
                break;
            default:
                $this->_sendResponse(501, sprintf('Error: Invalid request', $model));
                Yii::$app->end();
        }
        $this->renderJsonOutput($output);
    }

    public function actionUpdate($model, $id,$type=NULL)
    {
            
   
        if (isset($id) === false) {
            $this->renderJsonOutput(array('status' => EApiViewService::RESPONSE_NO, 'errorCode' => ErrorList::BAD_REQUEST, 'errorMsg' => 'Error: Parameter <b>id</b> is missing'));
        }
        $get = $_GET;
        $post = $_POST;
    
        if (empty($_POST)) {

            // application/json
            $post = BaseJson::decode($this->getPostData());
            
        } else {
            // application/x-www-form-urlencoded
            $post = $_POST;
        }
        $output = array('status' => EApiViewService::RESPONSE_NO, 'errorCode' => ErrorList::BAD_REQUEST, 'errorMsg' => 'Invalid request.');
        switch ($model) {
                case 'booking':
                switch ($type){
                    case 'cancelBooking':
                            $bookingMgr = new BookingManager();
                            $values=$post;
                            $values['token'] = $this->em_getallheaders();
                            $user = $this->userLoginRequired($values);
                            $userId=$user->getId();
                            if(empty($userId) || empty($id)){
                                $output['status'] = 'no';
                                $output['errorCode'] = EApiViewService::RESPONSE_VALIDATION_ERRORS;
                                $output['message'] = 'Wrong parameters';
                            }else{
                                $output = $bookingMgr->actionCancelBooking($id,$userId);
                            }
                    break;
                    default:
                        $this->_sendResponse(501, sprintf('Error: Invalid request', $type));
                        Yii::app()->end();
                }
                break;
                case 'cancelFile':
                        $values['token'] = $this->em_getallheaders();
                      //  $values['token']="D16DB100F05E58FF5CAE6DDF8077E96F";;
                        $user = $this->userLoginRequired($values);
                        $userId=$user->getId();
                        $bookMgr = new BookingManager();
                        $output = $bookMgr->cancelfile($id,$userId);
                break;
                case 'profile'://个人信息（基本信息）
                    if (isset($post['profile'])) {
                        $values = $post['profile'];
                        $values['userHostIp'] = Yii::app()->request->userHostAddress;
                        $user = $this->userLoginRequired($values);  // check if doctor has login.
                        $doctorMgr = new DoctorManager();
                        $output = $doctorMgr->apiCreateProfile($user, $values, $id);

                    } else {
                        $output['errorMsg'] = 'Wrong parameters.';
                    }
                break;
        } 
        $this->renderJsonOutput($output);
    }

    public function actionDelete($model, $id)
    {}
    public function getPostData() {
        return file_get_contents('php://input');
    }
    /**
     * 用户登录验证
     *
     * @param unknown $values            
     * @param string $pwd            
     * @return User
     */
    private function userLoginRequired($values)
    {
        $userMgr = new UserManager();
        $token = $userMgr->checkToken($values['token']);
        $output = new \stdClass();
        if($token === false){
            $output->errorCode = ErrorCode::ERROR_TOKEN;
            $output->errorMsg = ErrorCode::getErrText(ErrorCode::ERROR_TOKEN);
            $this->renderJsonOutput($output);
        }
        $user = $userMgr->getUserBytoken($token);

        if (!isset($user)) {
            $output->errorCode = ErrorCode::ERROR_USER_NOT_FOUND;
            $output->errorMsg = ErrorCode::getErrText(ErrorCode::ERROR_USER_NOT_FOUND);
            $this->renderJsonOutput($output);
        }else{
            return $user;
        }
    }
    

    private function _sendResponse($status = 200, $body = '', $content_type = 'text/html')
    {
        // set the status
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        header($status_header);
        // and the content type
        header('Content-type: ' . $content_type);
        
        // pages with body are easy
        if ($body != '') {
            // send the body
            echo $body;
        }  // we need to create the body if none is passed
else {
            // create some body messages
            $message = '';
            
            // this is purely optional, but makes the pages a little nicer to read
            // for your users. Since you won't likely send a lot of different status codes,
            // this also shouldn't be too ponderous to maintain
            switch ($status) {
                case 401:
                    $message = 'You must be authorized to view this page.';
                    break;
                case 404:
                    $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                    break;
                case 500:
                    $message = 'The server encountered an error processing your request.';
                    break;
                case 501:
                    $message = 'The requested method is not implemented.';
                    break;
            }
            
            // servers don't always have a signature turned on
            // (this is an apache directive "ServerSignature On")
            $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];
            
            // this should be templated in a real-world solution
            $body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                    <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
                </head>
                <body>
                    <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
                    <p>' . $message . '</p>
                    <hr />
                    <address>' . $signature . '</address>
                </body>
            </html>';
            
            echo $body;
        }
        Yii::$app->end();
    }

    private function _getStatusCodeMessage($status)
    {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented'
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

    private function _checkAuth()
    {
        // Check if we have the USERNAME and PASSWORD HTTP headers set?
        if (! (isset($_SERVER['HTTP_X_USERNAME']) and isset($_SERVER['HTTP_X_PASSWORD']))) {
            // Error: Unauthorized
            $this->_sendResponse(401);
        }
        $username = $_SERVER['HTTP_X_USERNAME'];
        $password = $_SERVER['HTTP_X_PASSWORD'];
        // Find the user
        $user = User::find('LOWER(username)=?', array(
            strtolower($username)
        ));
        if ($user === null) {
            // Error: Unauthorized
            $this->_sendResponse(401, 'Error: User Name is invalid');
        } else 
            if (! $user->validatePassword($password)) {
                // Error: Unauthorized
                $this->_sendResponse(401, 'Error: User Password is invalid');
            }
    }

    private function loadOverseasHospitalJson()
    {
        $overseasController = new OverseasController();
        
        $hospitals = array(
            array(
                'id' => 1,
                'name' => '新加坡伊丽莎白医院',
                'url' => '',
                'urlImage' => 'http://mingyihz.oss-cn-hangzhou.aliyuncs.com/static%2Foverseas_sg_elizabeth.jpg'
            ),
            array(
                'id' => 2,
                'name' => '新加坡邱德拔医院',
                'url' => '',
                'urlImage' => 'http://mingyihz.oss-cn-hangzhou.aliyuncs.com/static%2Foverseas_sg_ktph.jpg'
            ),
            array(
                'id' => 3,
                'name' => '新加坡中央医院',
                'url' => '',
                'urlImage' => 'http://mingyihz.oss-cn-hangzhou.aliyuncs.com/static%2Foverseas_sg_sgh.jpg'
            ),
            array(
                'id' => 4,
                'name' => '新加坡国立大学医院',
                'url' => '',
                'urlImage' => 'http://mingyihz.oss-cn-hangzhou.aliyuncs.com/static%2Foverseas_sg_nuh.jpg'
            )
        );
        $output = array(
            'hospitals' => array()
        );
        foreach ($hospitals as $hospital) {
            $obj = new \stdClass();
            foreach ($hospital as $key => $value) {
                $obj->{$key} = $value;
                $output['hospitals'][] = $obj;
            }
        }
        
        return $output;
    }

    private function parseQueryOptions($values)
    {
        $options = array();
        if (isset($values['offset']))
            $options['offset'] = $values['offset'];
        if (isset($values['limit']))
            $options['limit'] = $values['limit'];
        if (isset($values['order']))
            $options['order'] = $values['order'];
        return $options;
    }

    private function getApiVersionFromRequest()
    {

        return Yii::$app->request->get('api');
    }

    /**
     * 接收头信息
     * by 20160905
     */
    private function em_getallheaders()
    {
        if (! function_exists('getallheaders')) {
                
            function getallheaders()
            {
                foreach ($_SERVER as $name => $value) {
                    if (substr($name, 0, 5) == 'HTTP_') {
                        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                    }
                }
                return $headers;
            }
        }
        $hearders = getallheaders();

        $token = isset($hearders['Authorization']) ? $hearders['Authorization'] : '';
        return $token;
    }
    //加入过期判断
    private function token_expired($time_expiry){
         if (is_null($time_expiry)) {
            return true;
        } else {
            $now = time();
            return ($time_expiry > $now);
        }    
    }  
    //统一医院格式
    function loadformathospital($output){
        $output->errcode=0;
        $output->errorMsg= "success";
        $output->results=$output->hospitals;
        unset($output->hospitals);
        return $output;
    }
    
}
