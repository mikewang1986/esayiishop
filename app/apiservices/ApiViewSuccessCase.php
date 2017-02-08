<?php
namespace app\apiservices;
use Yii;
class ApiViewSuccessCase extends EApiViewService {
    public function __construct($id) {
        parent::__construct();
    }
    /**
     * loads data by the given $id (Disease.id).
     * @param integer $diseaeId Disease.id     
     */
    protected function loadData() {

        $this->loadSuccessCase();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output=new \stdClass();
            $this->output->status=self::RESPONSE_OK;
            $this->output->errorCode=0;
            $this->output->errorMsg='success';
            $this->output->results=$this->results;
        }
    }

    /**
     *
     * @throws CException
     */
    private function loadSuccessCase() {
        $data=Yii::$app->params['doctorcase']['case'];
        $this->results = $data;

    }


}
