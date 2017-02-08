<?php
namespace app\models;
use app\models\BookingCooperation;
use app\components\StatCode;
use Yii;
class OperationManager{
    public function createinformation($arr, $sendEmail = true){
        $output=new \stdClass();
        $output->status= 'no';
        $output->errorCode = 400;
        $model =new BookingCooperation();
        $model->contact_name = $arr['contact_name'];
        $model->sex = $arr['sex'];
        $model->age = $arr['age'];
        $model->disease_detail = $arr['disease_detail'];
        $model->mobile = $arr['mobile'];
       // $model->setAttributes($arr, true);
        $model->user_host_ip = Yii::$app->request->userHost;
        if(!empty($arr['doctor_name'])){
            $model->doctor_name = $arr['doctor_name'];
        }
        if(!empty($arr['date_booking'])){
            $model->date_booking=$arr['date_booking'];
        }
        if(!empty($arr['backup'])){
            $model->backup = $arr['backup'];
        }
	if(!empty($arr['nickname'])){
            $model->nickname = $arr['nickname'];
	}
        //来源
        $model->source = $arr['source'];
        //加入新内容
        if($model->source==StatCode::MA_CITY){
            //医生姓名
            if(!empty($arr['dr_name'])){
                 $model->dr_name = $arr['dr_name'];
            }
            //电话
            if(!empty($arr['doctor_mobile'])){
                $model->doctor_mobile=$arr['doctor_mobile'];
            }
            //科室
            if(!empty($arr['departments'])){
                $model->departments = $arr['departments'];
            }
            //医院
            if(!empty($arr['hosptial_name'])){
                $model->hosptial_name = $arr['hosptial_name'];
            }
        }
        $model->date_created=date("Y-m-d H:i:s");
        $model->date_updated=date("Y-m-d H:i:s");
        if ($model->save()) {   
            $output->status= 'ok';
            $output->errorCode = 200;
            $output->errorMsg = "success";
            $newid=new \stdClass();
            $newid->id=$model->getId();
            try {
                if ($sendEmail && isset($model)) {
                 // Send email to inform admin.
                    $testid=$newid->id;
                    if($model->source==StatCode::MA_CITY){
                        //$url = $_SERVER['HTTP_HOST']."/api/getrpc/".$testid."?source=".StatCode::MA_CITY;
                         $url = $_SERVER['HTTP_HOST']."/apiwap/getrpd/".$testid;
                    }
                    else{
                        $url = $_SERVER['HTTP_HOST']."/apiwap/getrpc/".$testid;
                    }

                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_URL,$url);
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_TIMEOUT, 1);
                    curl_exec($ch);
                    curl_close($ch);
                 }
            } catch (CException $ex) {
                    \Yii::error($ex->getMessage());
            } 
            $output->result=$newid;
            
        } else {
            $output->errorMsg = $model->getFirstErrors();
        }
        return $output;
    }
    
    public function rpc_call($id,$source="")
    {
        $rpc = new RPC();
        $client = $rpc->rpcClient(Yii::$app->params['rpcEmailUrl']);
        $wifi_email=Yii::app()->params['wifi_email']; 
        $email=Yii::app()->params['macity_email'];  
        //新浪爱问
        $sina_email=Yii::app()->params['sina_email']; 
        $bookingCooperation=BookingCooperation::model()->getById($id);
        if(empty($source)){
            if(!empty($wifi_email)){
                if($bookingCooperation->source==1){
                    foreach($wifi_email as $key=>$email){
                        $client->sendEmailCooperationBooking($id,$email);
                    }
                }
          
            }
            if(!empty($sina_email)){
                if($bookingCooperation->source==2){
                    foreach($sina_email as $key1=>$emailsina){
                        $client->sendEmailCooperationBooking($id,$emailsina);
                    }
                }
            }
        }
        else{
            $client->sendEmailCooperationBooking($id,$email);
        }
        $output=array('status' => "ok", 'results'=>null);
        return json_encode($output);
    }

}
