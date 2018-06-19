<?php

namespace common\extend\payment\lianlianpay;

use common\extend\payment\lianlianpay\LianlianpayConfig;
use common\extend\payment\lianlianpay\LianlianpayUtils;
use yii;

/**
 * LianlianpayNotify
 * 功能：连连支付通知处理类
 * 详细：处理连连支付各接口通知返回
 */
class LianlianpayNotify
{
    public $lianlianpayConfig;

    public function __construct()
    {
        $this->lianlianpayConfig = LianlianpayConfig::getConfig();
    }

    /**
     * 回调验证
     *
     * @param array $data
     * @return 验证结果
     */
    public function verifyCallback($data)
    {
        if (empty($data)) {//判断数组是否为空
            return false;
        } else {
            //生成签名结果
            $isSign = $this->getSignVerify($data, $data["sign"]);

            //写日志记录
            if ($isSign) {
                $isSignStr = 'true';
            } else {
                $isSignStr = 'false';
            }
            
            return $isSign;
        }
    }

    /**
     * 获取返回时的签名验证结果
     * @param $paraTemp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
    public function getSignVerify($paraTemp, $sign)
    {
        $signType = $paraTemp['sign_type'];
        //除去待签名参数数组中的空值和签名参数
        $paraFilter = LianlianpayUtils::paraFilter($paraTemp);

        //对待签名参数数组排序
        $paraSort = LianlianpayUtils::argSort($paraFilter);

        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = LianlianpayUtils::createLinkstring($paraSort);

        $isSign = false;
        switch (strtoupper(trim($signType))) {
            case "MD5" :
                $isSign = LianlianpayUtils::md5Verify($prestr, $sign, $this->lianlianpayConfig['key']);
                break;
            case "RSA" :
                $isSign = LianlianpayUtils::Rsaverify($prestr, $sign, trim($this->lianlianpayConfig['lianlian_public_key_path']));
                break;
            default :
                $isSign = false;
        }

        return $isSign;
    }
}
