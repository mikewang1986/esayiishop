<?php
namespace app\models;
use Yii;
use app\models\app\AppVersion;
use app\components\ErrorCode;
use app\components\StatCode;
class AppManager {
    // app footer nav 1.
    public function loadNav1Json() {
        $teamIntroLimit = 100;
        $output = array();
        // load top scrolling image and action url.
        $query = null;
        $options = null;
        $with = null;
        $teamMgr = new ExpertTeamManager();
        $teamList = $teamMgr->loadAllIExpertTeams($query, $options, $with);
        $teamIntroList = array();
        if (arrayNotEmpty($teamList)) {
            $counter = 1;
            foreach ($teamList as $iteam) {
                if ($counter > $teamIntroLimit) {
                    break;
                }
                if (strIsEmpty($iteam->introImageUrl) === false) {
                    $data = new \stdClass();
                    $data->teamId = $iteam->teamId;
                    $data->teamName = $iteam->teamName;
                    $data->teamCode = $iteam->teamCode;
                    $data->introImageUrl = $iteam->introImageUrl;
                    $data->teamUrl = $iteam->teamUrl;
                    $teamIntroList[] = $data;
                }
                $counter++;
            }
        }
        $output['teamList'] = $teamIntroList;

        // load faculties.
        $facultyMgr = new FacultyManager();
        $facultyList = $facultyMgr->loadFacultyList2();
        $output['facultyList'] = $facultyList;

        return $output;
    }

    // app footer nav 1.
    public function loadNav2Json() {
        // all expert team list.        
        $teamMgr = new ExpertTeamManager();
        $with = array('expteamMembers', 'expteamHospital', 'expteamHpDept', 'expteamFaculty');
        $output['expertTeams'] = $teamMgr->loadAllIExpertTeams(null, $with);
        return $output;
    }

    public function loadCurrentIOSUrl() {
        $appversionmodel=new AppVersion;
        $model = $appversionmodel->getLatestActiveVersionByOS('ios');
        if (isset($model)) {
            return $model->getAppDownloadUrl();
        } else {
            return '';
        }
    }

    public function loadCurrentAndroidUrl() {
        $appversionmodel=new AppVersion;
        $model = $appversionmodel->getLatestActiveVersionByOS('android');
        if (isset($model)) {
            return $model->getAppDownloadUrl();
        } else {
            return '';
        }
    }

    public function loadAppVersionJson($inputs) {
        $appVersion = $this->loadAppVersion($inputs);
        $output = array('appversion' => $appVersion);
        return $output;
    }

    public function loadAppVersion($inputs) {
        $output = array();
        $errors = $this->validateAppVersionInputs($inputs);
        if (empty($errors) === false) {
            $output['errors'] = $errors;
            return $output;
        }
        $appVersionNo = $inputs['app_version'];
        $os = $inputs['os'];
        $app_name = isset($inputs['app_name']) ? $inputs['app_name'] : StatCode::APP_NAME_MYZD;
        $appversionmodel=new AppVersion;
        $modelAppVersion = $appversionmodel->getLastestVersionByOSAndAppName($os, $app_name);
        if (isset($modelAppVersion) === false) {
            $errors['app_version'] = 'No data.';
            $output['errors'] = $errors;
            return $output;
        }

        $appObj = new \stdClass();
        $appObj->app_version = $appVersionNo;
        $appObj->cur_app_version = $modelAppVersion->getAppVersion();
        $appObj->cur_app_dl_url = $modelAppVersion->getAppDownloadUrl();
        $appObj->force_update = $modelAppVersion->getIsForceUpdate();
        $appObj->change_log = $modelAppVersion->getChangeLog();
        $appObj->cur_app_version_name = $modelAppVersion->app_version_name;

        return $appObj;
    }

    private function validateAppVersionInputs($inputs) {
        $errors = array();
        // Compulsory fields.
        $fields = array('os', 'os_version', 'device', 'app_version');
        foreach ($fields as $field) {
            if (isset($inputs[$field]) === false) {
                $errors[$field] = 'Missing ' . $field;
            }
        }
        if (empty($errors) === false) {
            return $errors;
        }
        // OS
        if ($inputs['os'] != 'ios' && $inputs['os'] != 'android') {
            $errors['os'] = 'Unknown os';
        }
        return $errors;
    }

}
