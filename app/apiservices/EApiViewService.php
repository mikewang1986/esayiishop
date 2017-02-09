<?php
namespace app\apiservices;
use yii\helpers\BaseJson;
use app\components\RsaEncrypter;
use app\models\core\CoreRsaConfig;
use Yii;
abstract class EApiViewService {
    const RESPONSE_NO = 'no';
    const RESPONSE_OK = 'ok';   //200
    const RESPONSE_NO_DATA = 'No data'; //400
    const RESPONSE_NOT_FOUND = 'Not found'; //404
    const RESPONSE_VALIDATION_ERRORS = 'Validation errors'; //400
    protected $results;
    public $output; // used for output data.
    public function __construct($value1 = null, $value2 = null, $value3 = null, $value4 = null) {
        $this->results = new \stdClass();
    }
    //基类修改
    public function loadApiViewData($pwd = false) {
        try {
            $this->loadData();
            $this->createOutput();
        } catch (\yii\db\Exception $cdbex) {
            //var_dump($cdbex->getMessage());
            //@TODO log.
            \Yii::error($cdbex->getMessage());
            $this->output = array('status' => self::RESPONSE_NO, 'error' => '数据错误', 'errorCode' => ErrorList::BAD_REQUEST, 'errorMsg' => '数据错误', 'results'=>null);
        } catch (\Exception $cex) {
            //var_dump($cex->getMessage());
            //@TODO log.
            \Yii::error($cex->getMessage());
            $this->output = array('status' => self::RESPONSE_NO, 'error' => $cex->getMessage(), 'errorCode' => ErrorList::BAD_REQUEST, 'errorMsg' => $cex->getMessage(), 'results'=>null);
        }
        // Converts array to stdClass object.
        if (is_array($this->output)) {
            $this->output = (object) $this->output;
        }
        
        if ($pwd) {
            $rasConfig = CoreRsaConfig::findOne()->getByClient("app");
            $stroutput = BaseJson::encode($this->output);
            $encrypet = new RsaEncrypter($rasConfig->public_key, $rasConfig->private_key);
            $sign = $encrypet->sign($stroutput); //base64 字符串加密
            $encrypet->verify($stroutput, $sign);
            $this->output = $encrypet->encrypt($stroutput);
        }
        return $this->output;
    }

    /**
     * @abstract method.     
     */
    protected abstract function loadData();

    /**
     * @abstract method     .
     */
    protected abstract function createOutput();
    /*
      protected function createOutput() {
      if (is_null($this->output)) {
      $this->output = array(
      'status' => self::RESPONSE_OK
      );
      }
      }
     * 
     */

    protected function createErrorOutput($errorMsg = "", $errorCode = 400) {
        $this->output = array(
            'status' => self::RESPONSE_NO,
            'errorCode' => $errorCode,
            'errorMsg' => $errorMsg
        );
    }

    protected function getTextAttribute($value, $ntext = true) {
        if ($ntext) {
            return Yii::$app->formatter->format($value);
        } else {
            return $value;
        }
    }

    /*
      protected function respond($httpCode, $status, $data = array()) {
      $response['status'] = $status;
      $response['data'] = $data;
      echo CJSON::encode($response);
      Yii::app()->end($httpCode, true);
      }
     */

    protected function throwNoDataException($msg = self::RESPONSE_NO_DATA) {
        throw new \Exception($msg);
    }
    
    public function printr($data){
        echo '<pre>';
        print_r(BaseJson::decode(BaseJson::encode($data)));
        echo '</pre>';
        exit;
        
    }

}
