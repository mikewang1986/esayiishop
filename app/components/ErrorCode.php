<?php
namespace app\components;
class ErrorCode
{
    const ERROR_NO=2100000;
    const ERROR_WRONG_PARAMETERS=2111001;
    const ERROR_BOOKING_BK_STATUS_DIFF=2110201;
    const ERROR_BOOKING_BK_STATUS_NOT_FROND=2110202;
    const ERROR_ORDER_NOT_FROND=2110203;
    const ERROR_BOOKING_NOT_FROND=2110204;
    const ERROR_WRONG_SQL_LINE=2198101;
    //
    const ERROR_IMAGE_FORMAT=2110211;
    const ERROR_IMAGE_VERIFICATIONCODE=2110212;
    const ERROR_IMAGE_VERIFICATIONCODE_ABATE=2110213;
	const ERROR_SMS_VERIFICATIONCODE=2110214;
    const ERROR_USER_REGISTER=2110215;
    const ERROR_USER_NOT_FOUND=2110216;
    const ERROR_USER_PASSWORD=2110217;
    const ERROR_ADMINBOOKING_QUICKBOOKINGCREATE=2110218;
    const ERROR_ADMINBOOKING_BOOKINGCREATE=2110219;
    const ERROR_WRONG_PAY=2110220;
    const ERROR_WRONG_SALEORDER_SAVE=2110221;
    const ERROR_WRONG_SALEORDERDATA_SAVE=2110222;
    const ERROR_WRONG_SALEORDER_NOTFREE=2110223;
    const ERROR_WRONG_ADMISSINOFORMATION=2110224;
    const ERROR_WRONG_STAT=2110225;
    const ERROR_WRONG_DEL_BOOKINGIMG=2110226;

    const ERROR_WRONG_SMSCODE_SEND=2110301;
    const ERROR_WRONG_SMSCODE_VERIFICATION=2110302;
    const ERROR_WRONG_USERNAME_REPEAT=2110303;
    const ERROR_TOKEN=2110304;
    public static $errCode=array(
        self::ERROR_NO => '成功',
        self::ERROR_WRONG_PARAMETERS => '参数错误',
        self::ERROR_BOOKING_BK_STATUS_DIFF => '订单状态错误',
        self::ERROR_ORDER_NOT_FROND=>'未找到订单号',
        self::ERROR_IMAGE_FORMAT=>'图片格式类型错误',
        self::ERROR_IMAGE_VERIFICATIONCODE=>'图形验证码错误',
        self::ERROR_IMAGE_VERIFICATIONCODE_ABATE=>'图形验证码已经失效',
		self::ERROR_SMS_VERIFICATIONCODE=>'短信验证码错误',
        self::ERROR_USER_REGISTER=>'用户注册失败',
        self::ERROR_USER_NOT_FOUND=>'用户不存在',
        self::ERROR_WRONG_SMSCODE_SEND=>'短信验证码发送失败',
        self::ERROR_WRONG_SMSCODE_VERIFICATION=>'短信验证码验证失败',
        self::ERROR_WRONG_USERNAME_REPEAT=>'该手机号已被注册',
        self::ERROR_WRONG_SQL_LINE=>'数据库操作失败',
        self::ERROR_BOOKING_NOT_FROND=>'未找到预约单',
        self::ERROR_BOOKING_BK_STATUS_NOT_FROND=>'预约单状态错误',
        self::ERROR_USER_PASSWORD=>'用户名或者密码错误',
        self::ERROR_TOKEN=>'用户未登陆',
        self::ERROR_ADMINBOOKING_QUICKBOOKINGCREATE=>'创建快速预约失败',
        self::ERROR_ADMINBOOKING_BOOKINGCREATE=>'创建预约失败',
        self::ERROR_WRONG_PAY=>'支付失败',
        self::ERROR_WRONG_SALEORDER_SAVE=>'支付信息保存失败',
        self::ERROR_WRONG_SALEORDERDATA_SAVE=>'支付数据保存失败',
        self::ERROR_WRONG_SALEORDER_NOTFREE=>'支付订单不是免费的',
        self::ERROR_WRONG_ADMISSINOFORMATION=>'提交信息失败',
        self::ERROR_WRONG_STAT=>'统计信息提交失败',
        self::ERROR_WRONG_DEL_BOOKINGIMG=>'删除图片失败',
    );
    public static function getErrText($err) {
        if (isset(self::$errCode[$err])) {
            return self::$errCode[$err];
        }else {
            return false;
        };
    }

}