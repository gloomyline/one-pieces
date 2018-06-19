<?php

namespace common\extend\payment\lianlianpay;

use common\services\CurlService;
use common\extend\payment\lianlianpay\LianlianpayConfig;
use common\extend\payment\lianlianpay\LianlianpayUtils;
use yii\helpers\Json;

/* *
 * LianlianPayApi
 * 功能：连连支付各接口请求类
 * 详细：构造连连支付各接口表单HTML文本，获取远程HTTP数据
 */
class LianlianpayApi
{

    private $lianlianpayConfig;

    const PATMENT_REQUEST_URL = 'https://instantpay.lianlianpay.com/paymentapi/payment.htm'; // 实时付款请求地址，注意：需http://格式的完整路径，不能加?id=123这类自定义参数
    const PATMENT_CONFIRM_URL = 'https://instantpay.lianlianpay.com/paymentapi/confirmPayment.htm'; // 确认付款
    const APP_REQUEST_ANDROID = 1;
    const APP_REQUEST_IOS = 2;
    const APP_REQUEST_WAP = 3;
    const USER_ID_PREFIX = 'wkfq_';
    const TEST_USER_ID_PREFIX = 'test_';

    const APP_ORDER_DES = '悟空分期-平台放款';// 订单描述（付款用途）
    const APP_CONSUME_ORDER_DES = '消费分期-平台放款';// 订单描述（付款用途）
    const PATMENT_MEMO = '悟空分期放款'; // 收款备注，传递至银行
    const FLAG_CARD_TO_PUBLIC = '1'; // 对公对私标识 0-对私 1-对公
    const FLAG_CARD_TO_PRIVATE = '0'; // 对公对私标识 0-对私 1-对公
    const PAY_TYPE_PAYMENT = '2'; // 支付类型：1-用户主动还款（认证支付）2-放款（实时支付）3-平台代扣（分期支付）
    const REQUEST_SUCCESS = '0000'; // 连连支付请求成功
    const NEED_QUERY_CODE_ARR = ['1002', '2005', '4006', '4007', '4009', '9999']; // 1002 商户付款流水号重复| 2005 原交易已在进行处理，请勿重复提交 | 4006 敏感信息加密异常 | 4009 验证码异常 | 9999 系统错误
    const NEED_CONFIRM_CODE_ARR = ['4002', '4003', '4004']; // 4002 意思重复提交订单 | 4003 收款银行卡和姓名不一致 | 4004 疑似重复提交订单且收款银行卡和姓名不一致

    const AUTHPAY_NAME_GOODS = '用户主动还款';  // 认证支付 - 商品名称
    const AUTHPAY_NAME_GOODS_BANKCARD_REPAYMENT = '银行卡还款扣款';  // 分期支付 - 商品名称
    const AUTHPAY_BUSI_PARTNER_VIRTUAL = '101001';  // 认证支付 商户业务类型 101001-虚拟商品销售 109001-实物商品销售
    const AUTHPAY_INFO_ORDER = '悟空分期-用户还款';  // 订单描述
    const AUTHPAY_ID_TYPE_IDENTIFICATION = '0'; // 证件类型：0-身份证

    /**
     * 连连支付网关地址
     */
    public $lianlianPayGateway = '';

    public function __construct($lianlian_pay_config)
    {
        $this->lianlianpayConfig = $lianlian_pay_config;
    }

    /**
     * 生成签名结果
     *
     * @param $paraSort 已排序要签名的数组
     * @return 签名结果字符串
     */
    public function buildRequestMysign($paraSort)
    {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = LianlianpayUtils::createLinkstring($paraSort);

        $mysign = "";
        switch (strtoupper(trim($this->lianlianpayConfig['sign_type']))) {
            case "MD5" :
                $mysign = LianlianpayUtils::md5Sign($prestr, $this->lianlianpayConfig['key']);
                break;
            case "RSA" :
                $mysign = LianlianpayUtils::Rsasign($prestr, $this->lianlianpayConfig['lianlian_private_key_path']);
                break;
            default :
                $mysign = "";
        }

        return $mysign;
    }

    /**
     * 生成要请求给连连支付的参数数组
     * @param $paraTemp 请求前的参数数组
     * @return 要请求的参数数组
     */
    public function buildRequestPara($paraTemp)
    {
        //除去待签名参数数组中的空值和签名参数
        $paraFilter = LianlianpayUtils::paraFilter($paraTemp);

        //对待签名参数数组排序
        $paraSort = LianlianpayUtils::argSort($paraFilter);

        //生成签名结果
        $mysign = $this->buildRequestMysign($paraSort);

        //签名结果与签名方式加入请求提交参数组中
        $paraSort['sign'] = $mysign;
        $paraSort['sign_type'] = strtoupper(trim($this->lianlianpayConfig['sign_type']));
        return $paraSort;
    }

    /**
     * 生成要请求给连连支付的参数数组
     * @param $paraTemp 请求前的参数数组
     * @return 要请求的参数数组字符串
     */
    public function buildRequestParaToString($paraTemp)
    {
        //待请求参数数组
        $para = $this->buildRequestPara($paraTemp);

        //把参数组中所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
        $requestData = LianlianpayUtils::createLinkstringUrlencode($para);

        return $requestData;
    }

    /**
     * 用户签约信息查询
     * @param $paraTemp 请求前的参数数组
     * @return 查询结果
     */
    public function queryBankcardByAgreeNo($paraTemp)
    {
        $queryUrl = 'https://queryapi.lianlianpay.com/bankcardbindlist.htm';
        $responseText = LianlianpayUtils::getHttpResponseJSON($queryUrl, $paraTemp);
        return $responseText;
    }

    /**
     * 认证支付支付结果查询服务接口
     * @param array $params
     * @return string 查询的结果
     */
    public function queryOrder($params)
    {
        $requestPara = $this->buildRequestPara($params);
        $res = CurlService::sendRequest('https://queryapi.lianlianpay.com/orderquery.htm', Json::encode($requestPara));
        return $res;
    }

    /**
     * 建立请求，以模拟远程HTTP的POST请求方式构造并获取连连支付的处理结果
     * @param array $request_data 请求参数数组
     * @param string $llpay_payment_url 请求地址
     * @return mixed 连连支付处理结果
     */
    public function buildRequestJSON($request_data, $llpay_payment_url) {
        $json = json_encode($request_data);
        $curl = curl_init($llpay_payment_url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //信任任何证书
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json))
        );
        $responseText = curl_exec($curl);
        curl_close($curl);
        return $responseText;
    }

    /**
     * 实时支付 付款结果查询
     * @param $params
     * @return \common\services\Ambigous
     */
    public function queryPayment($params)
    {
        $requestPara = $this->buildRequestPara($params);
        $res = $this->buildRequestJSON( $requestPara, 'https://instantpay.lianlianpay.com/paymentapi/queryPayment.htm');
        return $res;
    }

    /**
     * 银行卡卡 BIN 查询
     * @param $paraTemp 请求前的参数数组
     * @return 查询结果
     */
    public function queryBankcardBin($paraTemp)
    {
        $queryUrl = 'https://queryapi.lianlianpay.com/bankcardbin.htm';
        $responseText = LianlianpayUtils::getHttpResponseJSON($queryUrl, $paraTemp);
        return $responseText;
    }

    /**
     * 授权申请
     * @param array $paraTemp 请求前的参数数组
     * @return string 授权结果
     */
    public function agreenoAuthApply($paraTemp)
    {
        $queryUrl = 'https://repaymentapi.lianlianpay.com/agreenoauthapply.htm';
        $responseText = LianlianpayUtils::getHttpResponseJSON($queryUrl, $paraTemp);
        return $responseText;
    }

    /**
     * 银行卡还款扣款接口
     * @param array $paraTemp 请求前的参数数组
     * @return string 扣款结果
     */
    public function bankcardRepayment($paraTemp)
    {
        $queryUrl = 'https://repaymentapi.lianlianpay.com/bankcardrepayment.htm';
        $responseText = LianlianpayUtils::getHttpResponseJSON($queryUrl, $paraTemp);
        return $responseText;
    }
}
