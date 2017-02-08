<?php
namespace app\models;
use Yii;
use app\models\site\FeedBack;
class FeedbackManager {
    public function createfeedback($arr) {
        $output=new \stdClass();
        $output->status= 'no';
        $output->errorCode = 400;
        $arr['source']='wap_wifi';
        $model =new FeedBack();
        if(isset($arr['username']) && !empty($arr['username'])){
            $model->content = $arr['username'];
        }
        if(isset($arr['mobile'])  && !empty($arr['mobile'])){
            $model->contact_mobile = $arr['mobile'];
        }
        if(isset($arr['source']) && !empty($arr['source'])){
            $model->source = $arr['source'];
        }
        $model->user_host_ip = Yii::$app->request->userIP;
        $model->date_created=date("Y-m-d H:i:s");
        $model->date_updated=date("Y-m-d H:i:s");
        if ($model->save()) {
            $output->status= 'ok';
            $output->errorCode = 200;
            $output->errorMsg = "success";
            $newid=new \stdClass();
            $newid->id=$model->getId();
            $output->result=$newid;
        } else {
            $output->errorMsg = $model->getFirstErrors();
        }
        return $output;
    }
}
