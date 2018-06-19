<?php
namespace common\services;
use common\services\CurlService;

use Yii;

class LimuService
{
    const API_HOST = 'https://api.limuzhengxin.com/api/gateway'; // 正式环境
    const API_KEY = '8875708467978346'; // 正式环境
    const API_SECRET = 'NMvlfKxZThYB0b8E1sft8wlj7FlPjPCh'; // 正式环境
    //const API_HOST = 'https://t.limuzhengxin.cn:3443/api/gateway'; // 测试环境
    //const API_KEY = '8045959838303697'; // 测试环境
    //const API_SECRET = 'XbGUfBeo5OdnGCmtpbQr3m6OPNbWFk8Q'; // 测试环境
    const API_VERSION = '1.2.0';
    const API_SUCCESS_CODE = '0000';
    const API_ACCEPT_SUCCESS_CODE = '0010';
    const API_LOGIN_SUCCESS_CODE = '0100';
    const API_WAIT_INPUT_CODE = '0006';
    const API_BIZ_MOBILE = 'mobile';
    const API_BIZ_MOBILE_REPORT = 'mobileReport';
    const API_BIZ_JD = 'jd'; // 京东数据
    const API_BIZ_TAOBAO = 'taobao'; // 淘宝数据
    const API_BIZ_EDUCATION = 'education'; // 学历信息
    const API_BIZ_BILL = 'bill'; // 信用卡账单
    const API_BIZ_EBANK= 'ebank'; // 网银流水

    const API_LOGIN_TYPE_QR = 'qr'; // 手机扫描登录
    const API_LOGIN_TYPE_NORMAL = 'normal'; // 正常登录

    const API_BILL_TYPE_EMAIL = 'email'; // 账单类型：email-邮箱账单
    const API_ALL_BANK_CODE = 'ALL'; // 账单银行

    const API_SERVICE_URL_TEST_ENV = 'https://t.limuzhengxin.cn'; // 测试环境
    const API_SERVICE_URL_PRODUCT_ENV = 'https://api.limuzhengxin.com'; // 生产环境

    const LIMU_BILL_SUPPORT_EMAIL = ['163.com', '126.com', 'sina.com', 'sina.cn', 'qq.com', '139.com']; // 立木信用卡账单查询，支持邮箱账号类型

    //立木征信接口 通用状态查询（原用名:mobileRoundRobin）
    public static function commonStatusGet($token, $bizType)
    {
        //状态参数
        $params = array(
            "method" => "api.common.getStatus",
            'apiKey' => self::API_KEY,
            'version' => self::API_VERSION,
            "bizType" => $bizType,
            "token" => $token
        );

        //状态查询
        ksort($params);
        $request = self::buildRequest($params);
        $content = CurlService::sendRequest(self::API_HOST, $request);
        if ($content) {
            $result = json_decode($content);
            return $result;
        }
        return null;
    }

    //立木征信接口 通用发送验证码 （原用名:mobileSendSms）
    public static function commonSendSms($token, $input)
    {
        //发送短信参数
        $params = array(
            "method" => "api.common.input",
            'apiKey' => self::API_KEY,
            'version' => self::API_VERSION,
            "token" => $token,
            "input" => $input,
        );

        ksort($params);
        $request = self::buildRequest($params);
        $content = CurlService::sendRequest(self::API_HOST, $request);
        if ($content) {
            $result = json_decode($content);
            return $result;
        }
        return null;
    }

    // 立木征信接口 通用结果查询（原用名:getMobileCredit）
    public static function commonResultGet($token, $bizType)
    {

        $params = [
            'method' => 'api.common.getResult',
            'apiKey' => self::API_KEY,
            'version' => self::API_VERSION,
            "token"  => $token,
            "bizType" => $bizType
        ];

        ksort($params);
        $request = self::buildRequest($params);
        $content = CurlService::sendRequest(self::API_HOST, $request);
        if ($content) {
            return $content;
        }
        return null;
    }


    public static function getMobileArea($mobile)
    {
        $params = [
                'method' => 'api.mobile.area',
                'apiKey' => self::API_KEY,
                'version' => self::API_VERSION,
                'mobileNo' => $mobile,
            ];
        $request = self::buildRequest($params);
        $content = CurlService::sendRequest(self::API_HOST, $request);
        return $content;
    }

    public static function queryIdentity($realname, $identityNo)
    {
        $params = [
                'method' => 'api.identity.idcheck',
                'apiKey' => self::API_KEY,
                'version' => self::API_VERSION,
                'name' => $realname,
                'identityNo' => $identityNo
            ];
        ksort($params);
        $request = self::buildRequest($params);
        $content = CurlService::sendRequest(self::API_HOST, $request);
        if ($content) {
            $result = json_decode($content);
            return $result;
        }
        return null;
    }

    public static function queryMobile($realname, $serviceCode)
    {   
        $callBackUrl = Yii::$app->params['limu_callback_url'];
        $params = [
            'method' => 'api.mobile.get',
            'apiKey' => self::API_KEY,
            'version' => self::API_VERSION,
            'callBackUrl' => $callBackUrl,
            'username' => $realname,
            'password' => base64_encode($serviceCode)
        ];

        ksort($params);
        $request = self::buildRequest($params);
        $content = CurlService::sendRequest(self::API_HOST, $request);
        if ($content) {
            $result = json_decode($content);
            return $result;
        }
        return null;
    }

    public static function queryCentralBank($username, $password, $middleAuthCode)
    {
        $params = [
                'method' => 'api.credit.get',
                'apiKey' => self::API_KEY,
                'version' => '1.2.0',
                'username' => $username,
                'password' => base64_encode($password),
                'middleAuthCode' => $middleAuthCode,
            ];
        ksort($params);
        $request = self::buildRequest($params);
        $content = CurlService::sendRequest(self::API_HOST, $request);
        return $content;
    }

    public static function buildRequest($params)
    {
        $kvMap = [];
        $request = '';
        foreach ($params as $key => $value) {
            $item = $key . '=' . $value;
            $kvMap[] = $item;
        }
        if ($kvMap) {
            $signStr = implode("&", $kvMap);
            $signStr .= self::API_SECRET;
            //$sign = bin2hex(sha1($signStr));
            $sign = sha1($signStr);

            $request = implode("&", $kvMap) . '&sign=' . $sign;
            
        }
        return $request;
    }

    public static function getSign($params)
    {
        $kvMap = [];
        $sign = '';
        foreach ($params as $key => $value) {
            $item = $key . '=' . $value;
            $kvMap[] = $item;
        }
        if ($kvMap) {
            $signStr = implode("&", $kvMap);
            $signStr .= self::API_SECRET;
            $sign = bin2hex(sha1($signStr));
        }
        return $sign;
    }

    // 运营商报告采集任务提交
    public static function queryMobileReport($mobile, $serviceCode, $identityCardNo, $identityName)
    {
        $callBackUrl = Yii::$app->params['limu_callback_url'];
        $params = [
            'apiKey' => self::API_KEY,
            'callBackUrl' => $callBackUrl,
            'uid' => $mobile,
            'ts' => time() * 1000, // 时间戳 用户请求提交的时间戳（毫秒）
            'identityCardNo' => $identityCardNo, // 身份证号码
            'identityName' => $identityName, // 姓名
            'username' => $mobile, // 手机号码
            'password' => base64_encode($serviceCode) // 服务密码
        ];

        ksort($params);
        $request = self::buildRequest($params);
        $requestUrl = self::API_SERVICE_URL_PRODUCT_ENV . '/mobile_report/v1/task';
        $content = CurlService::sendRequest($requestUrl, $request);
        if ($content) {
            $result = json_decode($content);
            return $result;
        }
        return null;
    }
    //运营商报告采集状态查询
    public static function queryMobileReportTaskStatus($token)
    {
        //运营商采集状态参数
        $params = array(
            'apiKey' => self::API_KEY,
            "token" => $token
        );

        //状态查询
        ksort($params);
        $request = self::buildRequest($params);
        $requestUrl = self::API_SERVICE_URL_PRODUCT_ENV . '/mobile_report/v1/task/status';
        $content = CurlService::sendRequest($requestUrl, $request);
        if ($content) {
            $result = json_decode($content);
            return $result;
        }
        return null;
    }
    // 运营商报告验证码输入
    public static function mobileReportSendSms($token, $input)
    {
        //发送短信参数
        $params = array(
            'apiKey' => self::API_KEY,
            "token" => $token,
            "input" => $input,
        );

        ksort($params);
        $request = self::buildRequest($params);
        $requestUrl = self::API_SERVICE_URL_PRODUCT_ENV . '/mobile_report/v1/task/input';
        $content = CurlService::sendRequest($requestUrl, $request);
        if ($content) {
            $result = json_decode($content);
            return $result;
        }
        return null;
    }
    // 运营商报告结果获取
    public static function mobileReportGet($token)
    {
        $params = array(
            'apiKey' => self::API_KEY,
            "token" => $token,
        );

        ksort($params);
        $request = self::buildRequest($params);
        $requestUrl = self::API_SERVICE_URL_PRODUCT_ENV . '/mobile_report/v1/task/report';
        $content = CurlService::sendRequest($requestUrl, $request);
        if ($content) {
            $result = json_decode($content);
            return $result;
        }
        return null;
    }
    // 运营商报告原始数据获取
    public static function mobileReportBaseDataGet($token)
    {
        $params = array(
            'apiKey' => self::API_KEY,
            "token" => $token,
        );

        ksort($params);
        $request = self::buildRequest($params);
        $requestUrl = self::API_SERVICE_URL_PRODUCT_ENV . '/mobile_report/v1/task/data';
        $content = CurlService::sendRequest($requestUrl, $request);
        if ($content) {
            $result = json_decode($content);
            return $result;
        }
        return null;
    }

    // 京东查询
    public static function queryJd($mobile, $username, $password)
    {
        $callBackUrl = Yii::$app->params['limu_callback_url'];
        //京东查询参数
        $params = array(
            'method' => 'api.jd.get',
            'apiKey' => self::API_KEY,
            'callBackUrl' => $callBackUrl,
            'version' => self::API_VERSION,
            'uid' => $mobile,
            'ts' => time() * 1000, // 时间戳 用户请求提交的时间戳（毫秒）
            'username' => $username, // 京东用户帐号
            'password' => base64_encode($password), // 京东密码
        );

        //京东查询
        ksort($params);
        $request = self::buildRequest($params);
        $content = CurlService::sendRequest(self::API_HOST, $request);
        if ($content) {
            $result = json_decode($content);
            return $result;
        }
        return null;
    }
    // 淘宝查询
    public static function queryTaobao($mobile)
    {
        $callBackUrl = Yii::$app->params['limu_callback_url'];
        //淘宝查询参数
        $params = array(
            'method' => 'api.taobao.get',
            'apiKey' => self::API_KEY,
            'callBackUrl' => $callBackUrl,
            'version' => self::API_VERSION,
            'uid' => $mobile,
            'ts' => time() * 1000, // 时间戳 用户请求提交的时间戳（毫秒）
            'loginType' => self::API_LOGIN_TYPE_QR, // 登录方式 扫码登录
        );

        //淘宝查询
        ksort($params);
        $request = self::buildRequest($params);
        $content = CurlService::sendRequest(self::API_HOST, $request);
        if ($content) {
            $result = json_decode($content);
            return $result;
        }
        return null;
    }

    // 学历查询
    public static function queryEdu($mobile, $username, $password)
    {
        $callBackUrl = Yii::$app->params['limu_callback_url'];
        //学历查询参数
        $params = array(
            'method' => 'api.education.get',
            'apiKey' => self::API_KEY,
            'callBackUrl' => $callBackUrl,
            'version' => self::API_VERSION,
            'uid' => $mobile,
            'ts' => time() * 1000, // 时间戳 用户请求提交的时间戳（毫秒）
            'username' => $username, // 用户帐号
            'password' => base64_encode($password), // 密码
        );
        //学历查询
        ksort($params);
        $request = self::buildRequest($params);
        $content = CurlService::sendRequest(self::API_HOST, $request);
        if ($content) {
            $result = json_decode($content);
            return $result;
        }
        return null;
    }

    // 信用卡账单查询
    public static function queryCreditCardBill($mobile, $username, $password, $loginType)
    {
        $callBackUrl = Yii::$app->params['limu_callback_url'];
        //信用卡账单查询参数
        $params = array(
            'method' => 'api.bill.get',
            'apiKey' => self::API_KEY,
            'callBackUrl' => $callBackUrl,
            'version' => self::API_VERSION,
            'uid' => $mobile,
            'ts' => time() * 1000, // 时间戳 用户请求提交的时间戳（毫秒）
            'username' => $username, // 帐号
            'password' => base64_encode($password), // 密码
            'billType' => self::API_BILL_TYPE_EMAIL, // 账单类型
            'bankCode' => self::API_ALL_BANK_CODE, // 账单银行
            'loginType' => $loginType, // 登录类型
        );

        //信用卡账单查询
        ksort($params);
        $request = self::buildRequest($params);
        $content = CurlService::sendRequest(self::API_HOST, $request);
        if ($content) {
            $result = json_decode($content);
            return $result;
        }
        return null;
    }

    // 网银流水查询
    public static function queryEbank($mobile, $username, $password, $bankCode)
    {
        $callBackUrl = Yii::$app->params['limu_callback_url'];
        //网银流水查询参数
        $params = array(
            'method' => 'api.ebank.get',
            'apiKey' => self::API_KEY,
            'callBackUrl' => $callBackUrl,
            'version' => self::API_VERSION,
            'uid' => $mobile,
            'ts' => time() * 1000, // 时间戳 用户请求提交的时间戳（毫秒）
            'username' => $username, // 用户帐号
            'password' => base64_encode($password), // 密码
            'bankCode' => $bankCode, // 银行编号
        );
        //网银流水查询
        ksort($params);
        $request = self::buildRequest($params);
        $content = CurlService::sendRequest(self::API_HOST, $request);
        if ($content) {
            $result = json_decode($content);
            return $result;
        }
        return null;
    }

    // 获取银行对应编码
    public static function getBankCode($bankNo)
    {
        $bankArray = [
            'all' => [ 'bank_name' => '所有银行', 'bank_abbreviation' => 'ALL'],
            '01040000' => [ 'bank_name' => '中国银行', 'bank_abbreviation' => 'BOC'],
            '01020000' => [ 'bank_name' => '工商银行', 'bank_abbreviation' => 'ICBC'],
            '01050000' => [ 'bank_name' => '建设银行', 'bank_abbreviation' => 'CCB'],
            '01030000' => [ 'bank_name' => '农业银行', 'bank_abbreviation' => 'ABC'],
            '03010000' => [ 'bank_name' => '交通银行', 'bank_abbreviation' => 'BCM'],

            '03080000' => [ 'bank_name' => '招商银行', 'bank_abbreviation' => 'CMB'],
            '03090000' => [ 'bank_name' => '兴业银行', 'bank_abbreviation' => 'CIB'],
            '03060000' => [ 'bank_name' => '广发银行', 'bank_abbreviation' => 'CGB'],
            '03100000' => [ 'bank_name' => '浦发银行', 'bank_abbreviation' => 'SPDB'],
            '03070000' => [ 'bank_name' => '平安银行', 'bank_abbreviation' => 'PAB'],

            '04012900' => [ 'bank_name' => '上海银行', 'bank_abbreviation' => 'BOSC'],
            '03020000' => [ 'bank_name' => '中信银行', 'bank_abbreviation' => 'CITIC'],
            '03040000' => [ 'bank_name' => '华夏银行', 'bank_abbreviation' => 'HXB'],
            '03050000' => [ 'bank_name' => '民生银行', 'bank_abbreviation' => 'CMBC'],
            '03030000' => [ 'bank_name' => '光大银行', 'bank_abbreviation' => 'CEB'],

//            '01040000' => [ 'bank_name' => '花旗银行', 'bank_abbreviation' => 'CITIBANK'],
            '01000000' => [ 'bank_name' => '邮储银行', 'bank_abbreviation' => 'PSBC'],
        ];
        return $bankArray[$bankNo]['bank_abbreviation'] ?? '';
    }
}