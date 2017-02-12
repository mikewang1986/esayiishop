<?php

namespace app\models\adminbooking;
use app\models\EActiveRecord;
use Yii;

/**
 * This is the model class for table "admin_booking".
 *
 * @property integer $id
 * @property integer $booking_id
 * @property integer $booking_type
 * @property string $ref_no
 * @property integer $patient_id
 * @property string $patient_name
 * @property string $patient_mobile
 * @property string $patient_age
 * @property integer $patient_gender
 * @property string $patient_identity
 * @property integer $state_id
 * @property integer $city_id
 * @property string $patient_state
 * @property string $patient_city
 * @property string $patient_address
 * @property string $patient_relationship
 * @property string $disease_name
 * @property string $disease_detail
 * @property string $booking_detail
 * @property string $expected_time_start
 * @property string $expected_time_end
 * @property integer $expected_hospital_id
 * @property string $expected_hospital_name
 * @property integer $expected_hp_dept_id
 * @property string $expected_hp_dept_name
 * @property integer $expected_doctor_id
 * @property string $expected_doctor_name
 * @property string $expected_doctor_mobile
 * @property integer $creator_doctor_id
 * @property string $creator_doctor_name
 * @property string $creator_hospital_name
 * @property string $creator_dept_name
 * @property integer $final_doctor_id
 * @property string $final_doctor_name
 * @property string $final_doctor_mobile
 * @property integer $final_hospital_id
 * @property string $final_hospital_name
 * @property string $final_time
 * @property integer $operation_finished
 * @property integer $travel_type
 * @property integer $disease_confirm
 * @property string $customer_request
 * @property integer $customer_intention
 * @property integer $customer_type
 * @property integer $customer_type_pb
 * @property string $customer_diversion
 * @property string $customer_agent
 * @property string $business_partner
 * @property integer $is_commonweal
 * @property integer $booking_service_id
 * @property integer $is_buy_insurance
 * @property integer $is_deal
 * @property integer $is_calculate
 * @property integer $is_approval
 * @property integer $is_complete
 * @property integer $contact_id
 * @property string $contact_name
 * @property integer $booking_status
 * @property integer $work_schedule
 * @property integer $order_status
 * @property string $order_amount
 * @property string $total_amount
 * @property string $reference_price
 * @property string $deposit_total
 * @property string $deposit_paid
 * @property string $service_total
 * @property string $service_paid
 * @property integer $admin_user_id
 * @property string $admin_user_name
 * @property integer $bd_user_id
 * @property string $bd_user_name
 * @property integer $ope_user_id
 * @property string $ope_user_name
 * @property string $cs_explain
 * @property integer $doctor_accept
 * @property string $doctor_opinion
 * @property integer $doctor_user_id
 * @property string $doctor_user_name
 * @property string $date_related
 * @property integer $is_operated
 * @property string $remark
 * @property integer $display_order
 * @property integer $add_doctor_id
 * @property string $add_doctor_name
 * @property string $add_hospital_name
 * @property string $add_dept_name
 * @property integer $add_disease_id
 * @property string $add_disease_name
 * @property integer $surgery_id
 * @property string $surgery_name
 * @property integer $surgery_user_id
 * @property string $surgery_user_name
 * @property string $date_surgery
 * @property integer $num_repeat
 * @property integer $had_surgery
 * @property integer $docking_case
 * @property integer $summary_file_status
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 * @property integer $schedule_time
 * @property string $date_finish
 * @property string $date_invalid
 * @property string $date_cancel
 * @property integer $purpose
 */
class AdminBooking extends EActiveRecord
{
    const BK_TYPE_CRM = '0';
    const BK_TYPE_BK = '1';
    const BK_TYPE_PB = '2';
    const CUS_REQUEST_SHOUSHU = 'shoushu';
    const CUS_REQUEST_ZHUANZHEN = 'zhuanzhen';
    const CUS_REQUEST_WENZHEN = 'wenzhen';
    const CUS_REQUEST_MENZHEN = 'menzhen';
    const CUS_REQUEST_HUIZHEN = 'huizhen';
    const CUS_INTENTION_NORMAL = '1';
    const CUS_INTENTION_GOOD = '2';
    const CUS_INTENTION_GREAT = '3';
    const CUS_TYPE_UNSURE = 1;
    const CUS_TYPE_VALIDITY = 2;
    const CUS_TYPE_INVALID = 3;
    const CUS_DIVERSION_baidu = 'baidu';
    const CUS_DIVERSION_FRIEND = 'friend';
    const CUS_DIVERSION_DOCTOR = 'doctor';
    const CUS_DIVERSION_WELFARE = 'welfare';
    const CUS_AGENT_400 = 'phone400';
    const CUS_AGENT_BAIDU = 'baidu';
    const CUS_AGENT_WEBSITE = 'web';
    const CUS_AGENT_WAP = 'wap';
    const CUS_AGENT_WEIXIN = 'weixin';
    const CUS_AGENT_APP_IOS = 'ios';
    const CUS_AGENT_APP_ANDROID = 'android';
    const CUS_AGENT_BD = 'bd';
    const CUS_AGENT_DITUI = 'ditui';
    const CUS_AGENT_WEIBO = 'weibo';
    const CUS_AGENT_FRIEND = 'friend';
    const CUS_AGENT_DOCTOR = 'doctor';
    const CUS_AGENT_BJ_OFFICE = 'bj_office';
    const CUS_AGENT_TUOSHI = 'tuoshi';
    const DISEASE_CONFIRM_NO = 0;
    const DISEASE_CONFIRM_YES = 1;
    const ORDER_STATUS_NO = 0;
    const ORDER_STATUS_YES = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_booking';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['booking_id', 'booking_type', 'patient_id', 'patient_gender', 'state_id', 'city_id', 'expected_hospital_id', 'expected_hp_dept_id', 'expected_doctor_id', 'creator_doctor_id', 'final_doctor_id', 'final_hospital_id', 'operation_finished', 'travel_type', 'disease_confirm', 'customer_intention', 'customer_type', 'customer_type_pb', 'is_commonweal', 'booking_service_id', 'is_buy_insurance', 'is_deal', 'is_calculate', 'is_approval', 'is_complete', 'contact_id', 'booking_status', 'work_schedule', 'order_status', 'admin_user_id', 'bd_user_id', 'ope_user_id', 'doctor_accept', 'doctor_user_id', 'is_operated', 'display_order', 'add_doctor_id', 'add_disease_id', 'surgery_id', 'surgery_user_id', 'num_repeat', 'had_surgery', 'docking_case', 'summary_file_status', 'schedule_time', 'purpose'], 'integer'],
            [['expected_time_start', 'expected_time_end', 'final_time', 'date_related', 'date_surgery', 'date_created', 'date_updated', 'date_deleted', 'date_finish', 'date_invalid', 'date_cancel'], 'safe'],
            [['order_amount', 'total_amount', 'reference_price', 'deposit_total', 'deposit_paid', 'service_total', 'service_paid'], 'number'],
            [['ref_no', 'patient_name', 'expected_doctor_mobile', 'final_doctor_mobile', 'bd_user_name'], 'string', 'max' => 20],
            [['patient_mobile', 'patient_relationship', 'expected_hospital_name', 'expected_hp_dept_name', 'expected_doctor_name', 'creator_doctor_name', 'creator_hospital_name', 'creator_dept_name', 'final_doctor_name', 'customer_request', 'customer_diversion', 'customer_agent', 'business_partner', 'contact_name', 'admin_user_name', 'ope_user_name', 'doctor_user_name', 'add_doctor_name', 'add_disease_name', 'surgery_name', 'surgery_user_name'], 'string', 'max' => 50],
            [['patient_age'], 'string', 'max' => 11],
            [['patient_identity'], 'string', 'max' => 18],
            [['patient_state', 'patient_city'], 'string', 'max' => 10],
            [['patient_address', 'booking_detail'], 'string', 'max' => 200],
            [['disease_name', 'final_hospital_name', 'add_hospital_name', 'add_dept_name'], 'string', 'max' => 100],
            [['disease_detail'], 'string', 'max' => 1000],
            [['cs_explain', 'doctor_opinion'], 'string', 'max' => 500],
            [['remark'], 'string', 'max' => 2000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'booking_id' => '预约ID',
            'booking_type' => '预约类型',
            'ref_no' => '预约号',
            'patient_id' => '患者ID',
            'patient_name' => '患者姓名',
            'patient_mobile' => '患者手机',
            'patient_age' => '患者年龄',
            'patient_identity' => '患者身份证',
            'state_id' => '患者所在省ID',
            'city_id' => '患者所在市ID',
            'patient_state' => '患者所在省',
            'patient_city' => '患者所在市',
            'patient_address' => '患者地址',
            'disease_name' => '病情诊断',
            'disease_detail' => '病情描述',
            'expected_time_start' => '期望手术时间开始',
            'expected_time_end' => '期望手术时间结束',
            'expected_hospital_id' => '理想医院ID',
            'expected_hospital_name' => '理想医院',
            'expected_hp_dept_id' => '理想科室ID',
            'expected_hp_dept_name' => '理想科室',
            'expected_doctor_id' => '理想专家ID',
            'expected_doctor_name' => '理想专家',
            'creator_doctor_id' => '推送医生ID',
            'creator_doctor_name' => '推送医生姓名',
            'creator_hospital_name' => '推送医生医院',
            'creator_dept_name' => '推送医生科室',
            'final_doctor_id' => '手术医生ID',
            'final_doctor_name' => '手术医生',
            'final_hospital_id' => '手术医院ID',
            'final_hospital_name' => '手术医院ID',
            'final_time' => '最终手术时间',
            'disease_confirm' => '是否确诊',
            'customer_request' => '客户需求',
            'customer_intention' => '客户意向',
            'customer_type' => '客户类型',
            'customer_diversion' => '客户导流',
            'customer_agent' => '客户来源',
            'booking_status' => '预约状态',
            'order_status' => '付费状态',
            'order_amount' => '付费金额',
            'admin_user_id' => '业务员ID',
            'admin_user_name' => '业务员',
            'bd_user_id' => '地推人员ID',
            'bd_user_name' => '地推人员',
            'remark' => '备注',
            'display_order' => '排序序号',
            'date_created' => '创建日期',
            'date_updated' => '更新日期',
            'date_deleted' => '删除日期',
        ];
    }
    /**
     *  options getters
     */
    public function getOptionsBookingType() {
        return array(
            self::BK_TYPE_BK => '患者端预约',
            self::BK_TYPE_PB => '医生端预约',
            self::BK_TYPE_CRM => '人工添加预约',
        );
    }

    public function getOptionsCustomerRequest() {
        return array(
            self::CUS_REQUEST_SHOUSHU => '手术',
            self::CUS_REQUEST_ZHUANZHEN => '转诊',
            self::CUS_REQUEST_WENZHEN => '问诊',
            self::CUS_REQUEST_MENZHEN => '门诊',
            self::CUS_REQUEST_HUIZHEN => '会诊',
        );
    }

    public function getOptionsCustomerIntention() {
        return array(
            self::CUS_INTENTION_NORMAL => '一般',
            self::CUS_INTENTION_GOOD => '很好',
            self::CUS_INTENTION_GREAT => '特别好',
        );
    }

    public function getOptionsCustomerType() {
        return array(
            self::CUS_TYPE_UNSURE => '未确认的',
            self::CUS_TYPE_VALIDITY => '有效的',
            self::CUS_TYPE_INVALID => '无效的',
        );
    }

    public function getOptionsCustomerDiversion() {
        return array(
            self:: CUS_DIVERSION_baidu => '百度搜索',
            self:: CUS_DIVERSION_FRIEND => '熟人推荐',
            self:: CUS_DIVERSION_DOCTOR => '医生推荐',
            self:: CUS_DIVERSION_WELFARE => '公益项目',
        );
    }

    public function getOptionsCustomerAgent() {
        return array(
            self:: CUS_AGENT_400 => '400热线',
            self:: CUS_AGENT_BAIDU => '百度商桥',
            self:: CUS_AGENT_WEBSITE => '网站',
            self:: CUS_AGENT_WAP => '手机网站',
            self:: CUS_AGENT_WEIXIN => '微信',
            self:: CUS_AGENT_APP_IOS => '苹果APP',
            self:: CUS_AGENT_APP_ANDROID => '安卓APP',
            self:: CUS_AGENT_BD => 'BD',
            self:: CUS_AGENT_DITUI => '地推',
            self:: CUS_AGENT_WEIBO => '微博',
            self:: CUS_AGENT_FRIEND => '熟人',
            self:: CUS_AGENT_DOCTOR => '下级医生',
            self:: CUS_AGENT_BJ_OFFICE => '北京办介绍',
            self:: CUS_AGENT_TUOSHI => '拓实企业用户',
        );
    }

    public function getBookingType() {
        $options = self::getOptionsBookingType();
        if (isset($options[$this->booking_type])) {
            return $options[$this->booking_type];
        } else {
            return null;
        }
    }

    public function getCustomerRequest() {
        $options = self::getOptionsCustomerRequest();
        if (isset($options[$this->customer_request])) {
            return $options[$this->customer_request];
        } else {
            return null;
        }
    }

    public function getCustomerIntention() {
        $options = self::getOptionsCustomerIntention();
        if (isset($options[$this->customer_intention])) {
            return $options[$this->customer_intention];
        } else {
            return null;
        }
    }

    public function getCustomerType() {
        $options = self::getOptionsCustomerType();
        if (isset($options[$this->customer_type])) {
            return $options[$this->customer_type];
        } else {
            return null;
        }
    }

    public function getCustomerDiversion() {
        $options = self::getOptionsCustomerDiversion();
        if (isset($options[$this->customer_diversion])) {
            return $options[$this->customer_diversion];
        } else {
            return null;
        }
    }

    public function getCustomerAgent() {
        $options = self::getOptionsCustomerAgent();
        if (isset($options[$this->customer_agent])) {
            return $options[$this->customer_agent];
        } else {
            return null;
        }
    }

    public function getBookingStatue() {
        $options = StatCode::getOptionsBookingStatus();
        if (isset($options[$this->booking_status])) {
            return $options[$this->booking_status];
        } else {
            return null;
        }
    }

    public function getTravelType($text = true) {
        if ($text) {
            return StatCode::getBookingTravelType($this->travel_type);
        } else {
            return $this->travel_type;
        }
    }

    public static function getOptionsDiseaseConfirm() {
        return array(
            self::DISEASE_CONFIRM_NO => '否',
            self::DISEASE_CONFIRM_YES => '是',
        );
    }

    public function getDiseaseConfirm($v = true) {
        if ($v) {
            $options = self::getOptionsDiseaseConfirm();
            if (isset($options[$this->disease_confirm])) {
                return $options[$this->disease_confirm];
            } else {
                return null;
            }
        } else {
            $this->disease_confirm;
        }
    }

    public static function getOptionsOrderStatus() {
        return array(
            self::ORDER_STATUS_NO => '未付费',
            self::ORDER_STATUS_YES => '已付费',
        );
    }

    public function getOrderStatus($v = true) {
        if ($v) {
            $options = self::getOptionsOrderStatus();
            if (isset($options[$this->order_status])) {
                return $options[$this->order_status];
            } else {
                return null;
            }
        } else {
            $this->order_status;
        }
    }

    public function setFinalDoctorId($id) {
        $this->final_doctor_id = $id;
    }

    public function setFinalDoctorName($id) {
        $this->final_doctor_name = $id;
    }

    public static function getOptionsBookingStatus() {
        return array(
            StatCode::BK_STATUS_NEW => '待处理',
            StatCode::BK_STATUS_PROCESSING => '安排中',
            //            StatCode::BK_STATUS_CONFIRMED_DOCTOR => '专家已确认',
            //            StatCode::BK_STATUS_PATIENT_ACCEPTED => '患者已接受',
            StatCode::BK_STATUS_SERVICE_UNPAID => '待支付服务费',
            StatCode::BK_STATUS_SERVICE_PAIDED => '已完成（支付完服务费）',
            StatCode::BK_STATUS_PROCESS_DONE => '跟进结束',
            //StatCode::BK_STATUS_DONE => '已完成',
            StatCode::BK_STATUS_CHECKOUT => '已结账',
            StatCode::BK_STATUS_INVALID => '跟进无效',
            StatCode::BK_STATUS_NULLIFY => '作废',
            //StatCode::BK_STATUS_CANCELLED => '已取消'
            StatCode::BK_STATUS_REFUNDING => '申请退款',
            StatCode::BK_STATUS_REFUNDED => '已退款',
        );
    }
}
