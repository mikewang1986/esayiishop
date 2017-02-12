<?php
namespace app\models;
use Yii;
use app\models\payment\CoreLogPayment;
use app\apiservices\ErrorList;
use app\models\sales\SalesOrder;
use app\models\booking\Booking;
use app\models\booking\BookingServiceConfig;
use app\models\adminbooking\AdminBooking;
use app\components\StatCode;
class PaymentManager {
    public function BookingPayment(MedicalRecordBooking $mrBooking, $payMethod) {
        $payment = new MrBookingPayment();
        $payment->initModel($mrBooking);
        $payment->pay_method = $payMethod;
        $payment->user_host_ip = Yii::$app->request->getUserIP();
        if ($payment->save()) {
            $requestUrl = $this->createAlipayDirect($payment);
            if ($requestUrl !== null && $requestUrl != '') {
                $payment->setRequestUrl($requestUrl);
                $payment->setDateRequest('now');
                $payment->update(array('request_url', 'date_request'));
            } else {
                $payment->addError('requst_url', 'Failed to create payment request.');
            }
        }

        return $payment;
    }

    public function createAlipayDirect(MRBookingPayment $payment) {
        require_once("./protected/sdk/alipaydirect/alipay.config.php");
        require_once("./protected/sdk/alipaydirect/AlipaySubmit.php");

        /*
          $alipay_config['partner'] = '2088911587272042'; // TODO: store in db.
          //收款支付宝账号
          $alipay_config['seller_email'] = 'pay@mingyihz.com';    // TODO: store in db.
          //安全检验码，以数字和字母组成的32位字符
          $alipay_config['key'] = 'xdbffffm840h2d1sqou40t6lpwdfpege'; // TODO: store in db.
          //签名方式 不需修改
          $alipay_config['sign_type'] = strtoupper('MD5');
          //字符编码格式 目前支持 gbk 或 utf-8
          $alipay_config['input_charset'] = strtolower('utf-8');
          //ca证书路径地址，用于curl中ssl校验
          //请保证cacert.pem文件在当前文件夹目录中
          // $alipay_config['cacert'] = getcwd() . '\\cacert.pem';
          //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
          $alipay_config['transport'] = 'http';
         */
        $alipaySubmit = new AlipaySubmit($alipay_config);   // from alipay.config.php

        /*         * ************************请求参数************************* */

        //支付类型
        $payment_type = "1";
        //必填，不能修改
        //服务器异步通知页面路径
        //$notify_url = "http://mingyihz.com/api/payment/notify";
        //$notify_url = "http://test.mingyizd.com/payment/notify";
        $notify_url = "http://mingyihz.com/payment/notify?";
        //需http://格式的完整路径，不能加?id=123这类自定义参数
        //页面跳转同步通知页面路径
        //$return_url = "http://mingyihz.com/api/payment/return";
        //$return_url = "http://test.mingyizd.com/payment/return";
        $return_url = "http://mingyihz.com/payment/return?";
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
        //商户订单号
        $out_trade_no = $payment->getUID();
        //商户网站订单系统中唯一订单号，必填
        //订单名称
        $subject = $payment->getSubject();
        //必填
        //付款金额
        $total_fee = $payment->getBillAmount();
        //必填
        //订单描述
        $body = $payment->getDescription();
        //商品展示地址
        $show_url = '';
        //需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html
        //防钓鱼时间戳
        $anti_phishing_key = "";
        //若要使用请调用类文件submit中的query_timestamp函数
        //客户端的IP地址
        $exter_invoke_ip = "";
        //非局域网的外网IP地址，如：221.0.0.1
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "create_direct_pay_by_user",
            "partner" => trim($alipay_config['partner']),
            "seller_email" => trim($alipay_config['seller_email']),
            "payment_type" => $payment_type,
            "notify_url" => $notify_url,
            "return_url" => $return_url,
            "out_trade_no" => $out_trade_no,
            "subject" => $subject,
            "total_fee" => $total_fee,
            "body" => $body,
            "show_url" => $show_url,
            "anti_phishing_key" => $anti_phishing_key,
            "exter_invoke_ip" => $exter_invoke_ip,
            "_input_charset" => trim(strtolower($alipay_config['input_charset']))
        );

        //Build Request.        
        $requestParamStr = $alipaySubmit->buildRequestParaToString($parameter);
        $request_url = $alipaySubmit->alipay_gateway_new . $requestParamStr;

        return $request_url;
    }

    public function updateAlipayReturn() {
        /*
          require_once("./protected/sdk/alipaydirect/alipay.config.php");
          require_once("./protected/sdk/alipaydirect/AlipayNotify.php");
          $alipayNotify = new AlipayNotify($alipay_config);   // from alipay.config.php.
         * 
         */
        $alipayNotify = $this->createAlipayNotify();
        $verifyResult = $alipayNotify->verifyReturn();
        //$verifyResult=true;
        if ($verifyResult) {
            // MRBookingPayment.uid
            $request = Yii::app()->request;
            $out_trade_no = $request->getQuery('out_trade_no', null);

            $payment = $this->loadPaymentByUID($out_trade_no);

            //支付宝交易号
            $trade_no = $request->getQuery('trade_no', null);
            //交易状态
            $trade_status = $request->getQuery('trade_status', null);

            // update $payment->vendor_trade_no and vendor_trade_status.
            $payment->setVendorTradeNo($trade_no);
            $payment->setVendorTradeStatus($trade_status);
            $payment->setReturnData($request->getQueryString());
            $payment->setDateReturn('now');
            $payment->setErrorMsg($request->getQuery('error_code'));

            if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                $payment->setStatusPaid();
                $payment->setPaidDate('now');
                $payment->setPaidAmount($request->getQuery('total_fee', 0));
                $payment->setPaidCurrency($payment->getBillCurrency());
            }
            // Update payment record in db.
            if ($payment->update()) {
                //$payment = $this->loadPaymentByUID($payment->getUID('mrbpUser', 'mrbpBooking'));
            }

            return $payment;
        } else {
            return false;
        }
    }

    public function updateAlipayNotify() {
        /*
          require_once("./protected/sdk/alipaydirect/alipay.config.php");
          require_once("./protected/sdk/alipaydirect/AlipayNotify.php");

          //计算得出通知验证结果
          $alipayNotify = new AlipayNotify($alipay_config);
         * 
         */
        //计算得出通知验证结果
        $alipayNotify = $this->createAlipayNotify();
        $verify_result = $alipayNotify->verifyNotify();

        if ($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            //商户订单号
            $request = Yii::app()->request;
            $out_trade_no = $request->getPost('out_trade_no');

            //支付宝交易号
            $trade_no = $request->getPost('trade_no');

            //交易状态
            $trade_status = $request->getPost('trade_status');

            // update $payment->vendor_trade_no and vendor_trade_status.
            $payment->setVendorTradeNo($trade_no);
            $payment->setVendorTradeStatus($trade_status);
            $payment->setNotifyData(CJSON::encode($_POST));
            $payment->setDateReturn('now');
            $payment->setErrorMsg($request->getPost('error_code'));

            /* TODO: implement the following:
              if ($trade_status == 'TRADE_FINISHED') {
              //判断该笔订单是否在商户网站中已经做过处理
              //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
              //如果有做过处理，不执行商户的业务程序
              //注意：
              //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
              //调试用，写文本函数记录程序运行情况是否正常
              //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
              } else if ($trade_status == 'TRADE_SUCCESS') {
              //判断该笔订单是否在商户网站中已经做过处理
              //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
              //如果有做过处理，不执行商户的业务程序
              //注意：
              //付款完成后，支付宝系统发送该交易状态通知
              //调试用，写文本函数记录程序运行情况是否正常
              //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
              }
             */
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                $payment->setStatusPaid();
                $payment->setPaidDate('now');
                $payment->setPaidAmount($request->getQuery('total_fee', 0));
                $payment->setPaidCurrency($payment->getBillCurrency());
            }
            // Update payment record in db.
            if ($payment->update()) {
                //$payment = $this->loadPaymentByUID($payment->getUID('mrbpUser', 'mrbpBooking'));
            }
            return $payment->update();
            // echo "success";  //请不要修改或删除
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
            //echo "fail";
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            return false;
        }
    }

    public function createAlipayNotify() {
        require_once("./protected/sdk/alipaydirect/alipay.config.php");
        require_once("./protected/sdk/alipaydirect/AlipayNotify.php");

        $alipayNotify = new AlipayNotify($alipay_config);
        return $alipayNotify;
    }

    public function loadPaymentByUID($uid, $with = null) {
        if (is_null($with)) {
            $with = array('mrbpBooking', 'mrbpUser');
        }
        $model = MrBookingPayment::model()->getByUID($uid, $with);
        if (is_null($model)) {
            throw new CHttpException(404, 'Record is not found.');
        } else {
            return $model;
        }
    }

    //ping 支付提交
    public function payping($post){
            require_once(Yii::$app->basePath.'/sdk/pingpp-php-master/init.php');
            $output = new \stdClass();
            $output->errorMsg = null;
                $output->pingCharge = null;
                if (isset($post)) {
                    try {
                        $urlarray=array();
                        if(isset($post['success_url'])){
                            $urlarray['success_url']=$post['success_url'];
                        }
                        $refno=$post['order_no'];
                        $channel=$post['channel'];
                        $refurl=$post['ref_url'];
                        $payMgr = new PayManager();
                        $output->pingCharge  = $payMgr->doPingxxPay($refno, $channel, $refurl,$urlarray);
                    } catch (\Pingpp\Error\Base $e) {
                            $output->errorCode = ErrorList::PAY_ERROR;
                            $output->errorMsg = $e->getHttpBody();
                            \Yii::error($output->errorMsg);
                     } catch (\yii\db\Exception $cdbex) {
                            $output->errorCode = ErrorList::PAY_ERROR;
                            $output->errorMsg = 'error loading data';
                             \Yii::error($cdbex->errorMsg);
                    } catch (\Exception $cex) {
                            $output->errorCode = ErrorList::PAY_ERROR;
                            $output->errorMsg = $cex->getMessage();
                        \Yii::error($cex->getMessage());
                    }    
                }
                else{
                    $output->errorMsg = 'Wrong parameters.';
                }
                header('Content-Type: application/json; charset=utf-8');
                echo $output->pingCharge;
                CoreLogPayment::log($output->pingCharge, CoreLogPayment::LEVEL_INFO, Yii::$app->request->url, __METHOD__);
                exit;
    }
     //加入订单成功后的操作
    public function PayDeposit($post){
        $output = array('status' => 'no','errorCode' => 0,'errorMsg' =>'' ,'results' => array()); // default status is false.
        if($post['bk_ref_no']){
            $refno = $post['bk_ref_no'];
            $salemodel=new SalesOrder();
            $model = $salemodel->getByAttributes(array('bk_ref_no' => $refno ,'order_type' => 'deposit'));
            if($model){
                $bookmodel=new Booking;
                $booking =$bookmodel->getByRefNo($model->bk_ref_no);
                if ($booking->booking_service_id == BookingServiceConfig::BOOKING_SERVICE_FREE_LIINIC) {
                    $adminbookingmodel=new AdminBooking();
                    $adminDate = $adminbookingmodel->updateAllByAttributes(array('booking_status'=> StatCode::BK_STATUS_PROCESSING,'work_schedule'=> StatCode::BK_STATUS_PROCESSING ,'date_updated'=>new \yii\db\Expression("NOW()")), array('ref_no' =>$refno),'app\models\adminbooking\AdminBooking');
                    $orderDate =  $salemodel->updateAllByAttributes(array('is_paid'=> SalesOrder::ORDER_PAIDED,'date_closed'=> new \yii\db\Expression("NOW()")), array('bk_ref_no' => $refno ,'order_type' => 'deposit'),'app\models\sales\SalesOrder');
                    $bookingDate = $bookmodel->updateAllByAttributes(array('bk_status'=>  StatCode::BK_STATUS_PROCESSING), array('ref_no' => $refno),'app\models\booking\Booking');
                    if ($bookingDate && $orderDate && $adminDate) {
                         $output['status'] = 'ok';
                         $output['error_code'] = 200;
                         $output['errorMsg'] = 'no';
                         //$this->renderJsonOutput($output);
                    }
                } else {
                     $output['error_code'] = 200;
                     $output['errorMsg'] = 'SaleOrder is not free.';
                }
            }else{
                 $output['error_code'] = 200;
                 $output['errorMsg'] = 'SaleOrder not found.';

            }
            
        }else{
            $output['error_code'] = 200;
            $output['errorMsg'] = 'Wrong parameters.';
        }
        return $output;
    }
}
