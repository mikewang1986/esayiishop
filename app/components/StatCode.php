<?php
namespace app\components;
class StatCode {

    const ERROR_UNKNOWN = '未知';
    // gender.
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    // user role
    const USER_ROLE_PATIENT = 1;    // 患者
    const USER_ROLE_DOCTOR = 2; // 医生
    const USER_ROLE_MR = 3;     //医药代表  medical representative.
    const USER_ROLE_ADMIN = 9;    // 管理员
    const BK_TYPE_DOCTOR = 1;     // 预约某个医生
    const BK_TYPE_EXPERTTEAM = 2; // 预约专家团队
    const BK_TYPE_DEPT = 3; // 预约医院科室
    const BK_TYPE_QUICKBOOK = 9;    // 快速预约
    const BK_TYPE_WIFI = 10;    // WIFI预约
    const BK_TRAVELTYPE_PATIENT_GO = 1;    // 患者过去
    const BK_TRAVELTYPE_DOCTOR_COME = 2;   // 医生过来
    const BK_STATUS_NEW = 1;         // 待支付 旧版待处理
    const BK_STATUS_PROCESSING = 2;   //安排中 旧版处理中
    const BK_STATUS_CONFIRMED_DOCTOR = 5;   //待确认 旧版已确认专家
    const BK_STATUS_PATIENT_ACCEPTED = 6;   //待点评 旧版患者已接受
    const BK_STATUS_REFUNDING = 7; //申请退款
    const BK_STATUS_DONE = 8;        // 已完成
    const BK_STATUS_CANCELLED = 9;   // 已取消
    const BK_STATUS_REFUNDED = 10; //已退款
    const DR_C_TITLE_ZHUREN = 1;        // 主任
    const DR_C_TITLE_ZHUREN_ASSOC = 2;  // 副主任
    const DR_C_TITLE_ZHUZHI = 3;        // 主治
    const DR_C_TITLE_ZHUYUANYISHI = 4;  // 住院医师
    const DR_A_TITLE_PROF = 1;          // 教授
    const DR_A_TITLE_PROF_ASSOC = 2;    // 副教授
    const DR_A_TITLE_NONE = 9;          // 无    
    const TRAN_STATUS_UNPAID = 0;
    const TRAN_STATUS_PAID = 1;
    const TRANS_TYPE_AB = 0;          // booking.
    const TRANS_TYPE_BK = 1;          // booking.
    const TRANS_TYPE_PB = 2;          // patient_booking.

    const PAY_UNPAID = 0;           //未支付
    const PAY_SUCCESS = 1;          //支付成功
    const PAY_FAIL = 2;             //支付失败
    const MR_REPORTTYPE_MR = 'mr';    // 病历  Medical Record - PatientMRFile.reprot_type
    const MR_REPORTTYPE_DA = 'dc';    // 出院小结  Discharge Certificate - PatientMRFile.reprot_type
    const USER_AGENT_WEBSITE='website';
    const USER_AGENT_WEIXIN = 'weixin';  //预约来源为微信
    const USER_AGENT_MOBILEWEB = 'wap'; // 手机网站
    const USER_AGENT_APP_ANDROID = 'android';
    const USER_AGENT_APP_IOS = 'ios';
    const APP_NAME_MYZD='myzd'; // 患者版
    const APP_NAME_MOBILEDOCTOR='md';   // 医生版
    const HAS_REMOTE = 1;
    const HASNOT_REMOTE = 0;
    const MEDICAL_INSURANCE=1;//安庆医保项目
    const SINA_ASKED=2;//新浪爱问
    const MA_CITY=3;//
    const QUESTIONNAIRE_PICTURE_EXISTENCE = "已上传图片";    // 图片存在为 1
    const QUESTIONNAIRE_PICTURE_WITHOUT = "未上传图片"; // 图片不存在 0
    const PAY_HEADER= 'appheader';

    // gender.

    public static function getOptionsGender() {
        return array(
            self::GENDER_MALE => '男',
            self::GENDER_FEMALE => '女'
        );
    }

    //email

    public static function getEmailTo() {
        return array('jack.li@mingyizhudao.com', 'yujun.wang@mingyizhudao.com');
    }

    public static function getGender($gender) {
        $options = self::getOptionsGender();
        if (isset($options[$gender])) {
            return $options[$gender];
        } else {
            return self::ERROR_UNKNOWN;
        }
    }

    // Booking.type
    public static function getOptionsBookingType() {
        return array(
            self::BK_TYPE_DOCTOR => '医生',
            self::BK_TYPE_EXPERTTEAM => '专家团队',
            self::BK_TYPE_QUICKBOOK => '快速预约'
        );
    }

    public static function getBookingType($type) {
        $options = self::getOptionsBookingType();
        if (isset($options[$type])) {
            return $options[$type];
        } else {
            return self::ERROR_UNKNOWN;
        }
    }

    // PatientBooking.travel_type    
    public static function getOptionsBookingTravelType() {
        return array(
            self::BK_TRAVELTYPE_PATIENT_GO => '邀请医生过来',
            self::BK_TRAVELTYPE_DOCTOR_COME => '希望转诊治疗'
        );
    }

    public static function getBookingTravelType($travelType) {
        $options = self::getOptionsBookingTravelType();
        if (isset($options[$travelType])) {
            return $options[$travelType];
        } else {
            return self::ERROR_UNKNOWN;
        }
    }

    // Booking.status
    public static function getOptionsBookingStatus() {
        return array(
            self::BK_STATUS_NEW => '待支付',
            self::BK_STATUS_PROCESSING => '安排中',
            self::BK_STATUS_CONFIRMED_DOCTOR => '待确认',
            self::BK_STATUS_PATIENT_ACCEPTED => '待点评',
            self::BK_STATUS_DONE => '已完成',
            self::BK_STATUS_CANCELLED => '已取消',
            self::BK_STATUS_REFUNDING => '申请退款',
            self::BK_STATUS_REFUNDED => '已退款',
        );
    }

    public static function getBookingStatus($status) {
        $options = self::getOptionsBookingStatus();
        if (isset($options[$status])) {
            return $options[$status];
        } else {
            return self::ERROR_UNKNOWN;
        }
    }

    // Doctor.clinical_title
    public static function getOptionsClinicalTitle() {
        return array(
            self::DR_C_TITLE_ZHUREN => '主任医师',
            self::DR_C_TITLE_ZHUREN_ASSOC => '副主任医师',
            self::DR_C_TITLE_ZHUZHI => '主治医师',
            self::DR_C_TITLE_ZHUYUANYISHI => '住院医师'
        );
    }

    public static function getClinicalTitle($title) {
        $options = self::getOptionsClinicalTitle();
        if (isset($options[$title])) {
            return $options[$title];
        } else {
            return self::ERROR_UNKNOWN;
        }
    }

    // Doctor.academic_title
    public static function getOptionsAcademicTitle() {
        return array(
            self::DR_A_TITLE_PROF => '教授',
            self::DR_A_TITLE_PROF_ASSOC => '副教授',
            self::DR_A_TITLE_NONE => '无'
        );
    }

    public static function getAcademicTitle($title) {
        $options = self::getOptionsAcademicTitle();
        if (isset($options[$title])) {
            return $options[$title];
        } else {
            return self::ERROR_UNKNOWN;
        }
    }

    // SalesOrder.is_paid
    public static function getOptionsTransactionStatus() {
        return array(
            self::TRAN_STATUS_UNPAID => '未付款',
            self::TRAN_STATUS_PAID => '已付款',
        );
    }

    public static function getTransactionStatus($status) {
        $options = self::getOptionsTransactionStatus();
        if (isset($options[$status])) {
            return $options[$status];
        } else {
            return self::ERROR_UNKNOWN;
        }
    }

    // SalesPayment.payment_status
    public static function getOptionsPaymentStatus() {
        return array(
            self::PAY_UNPAID => '未支付',
            self::PAY_SUCCESS => '支付成功',
            self::PAY_FAIL => '支付失败'
        );
    }

    public static function getPaymentStatus($status) {
        $options = self::getOptionsPaymentStatus();
        if (isset($options[$status])) {
            return $options[$status];
        } else {
            return self::ERROR_UNKNOWN;
        }
    }

}
