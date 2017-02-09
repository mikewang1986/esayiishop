<?php
namespace app\models;
use Yii;
class StatManager {
    public function createPatientStat($values) {
        $output['status'] = 'no';
        $output['errorCode'] = 400;
        $model = new PatientStatLogMongo();
        $model->setAttributes($values, true);
        $model->date_created=date("Y-m-d H:i:s");
        if ($model->save()) {
                $output['status'] = 'ok';
                $output['errorCode'] = 200;
                $output['errorMsg'] = "success";
                //$output['result']['id'] = $model->getId();
        } else {
           // $output['errorMsg'] = $model->getFirstErrors();
        }
        return $output;
    }

    public function createAppLogStat($arr) {
        $output['status'] = 'no';
        $output['errorCode'] = 400;
        $model = new AppLogMongo();
        if(isset($arr['open_booking']) && $arr['open_booking'] > 0){
            $model->open_booking = $arr['open_booking'];
        }
        if(isset($arr['username']) && !empty($arr['username'])){
            $model->username = $arr['username'];
        }
        if(isset($arr['question']) && $arr['question'] > 0){
            $model->question = $arr['question'];
        }
        if(isset($arr['answer']) && $arr['answer'] > 0){
            $model->answer = $arr['answer'];
        }
        if(isset($arr['source']) && $arr['source'] > 0){
            $model->source = $arr['source'];
        }
        if (Yii::app()->session['vendorId']) {
            if(Yii::app()->session['vendorSite']){
                $model->site = Yii::app()->session['vendorSite'];
            }
            $model->vendor_id = Yii::app()->session['vendorId'];
        }
        $model->user_host_ip = Yii::app()->request->getUserHostAddress();
        $model->url = Yii::app()->request->getUrl();
        $model->url_referrer = Yii::app()->request->getUrlReferrer();
        $model->user_agent = Yii::app()->request->getUserAgent();
        $model->user_host = Yii::app()->request->getUserHost();
        $model->date_created=date("Y-m-d H:i:s");
        if ($model->save()) {
            $output['status'] = 'ok';
            $output['errorCode'] = 200;
            $output['errorMsg'] = "success";
//            $output['result']['id'] = $model->getId();
        } else {
            $output['errorMsg'] = $model->getFirstErrors();
        }
        return $output;
    }
}
