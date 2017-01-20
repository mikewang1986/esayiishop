<?php

class ApiViewBookingFile extends EApiViewService {

    private $bookingId;
    private $files;

    public function __construct($bookingId) {
        parent::__construct();
        $this->bookingId = $bookingId;
        $this->files = array();
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

    protected function loadData() {
        $this->loadBookingFile();
    }

    private function loadBookingFile() {
        $models = BookingFile::model()->getAllByAttributes(array('booking_id' => $this->bookingId));
        if (arrayNotEmpty($models)) {
            $this->setBookingFile($models);
        }
        $this->results->files = $this->files;
    }

    private function setBookingFile($models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->absFileUrl = $model->getAbsFileUrl();
            $data->absThumbnailUrl = $model->getAbsThumbnailUrl();
            $this->files[] = $data;
        }
    }

}
