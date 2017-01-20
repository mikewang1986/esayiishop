<?php

class ApiViewBookingStatus extends EApiViewService {
    protected function loadData() {
        // load
        $this->loadBookingStatus();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->results,
            );
        }
    }

    private function loadBookingStatus() {
        $status = StatCode::getOptionsBookingStatus();
        $this->setBookingStatus($status);
    }

    private function setBookingStatus(array $status) {
        $data = new stdClass();
        $data->bk_status = 0;
        $data->status_name = '全部';
        $this->results->status[] = $data;
        foreach ($status as $k=>$v) {
            $data = new stdClass();
            $data->bk_status = $k;
            $data->status_name = $v;
            $this->results->status[] = $data;
        }
    }

}
