<?php

namespace common\extend\payment\lianlianpay;

use Yii;

/**
 * 连连支付配置类
 */
class LianlianpayConfig
{
    /**
     * 取得连连支付配置数组
     * @return array 返回连连支付配置数组
     */
    public static function getConfig()
    {
        //商户编号
        $lianlianpayConfig['oid_partner'] = '201709220000944008';
        //商户的私钥
        $lianlianpayConfig['lianlian_private_key_path']   = dirname(__FILE__) . '/rsa_private_key.pem';
        //RSA验签使用的连连支付公钥文件地址
        $lianlianpayConfig['lianlian_public_key_path'] = dirname(__FILE__) . '/rsa_public_key.pem';
        //签名方式 不需修改
        $lianlianpayConfig['sign_type'] = strtoupper('RSA');
        //安全检验码，以数字和字母组成的字符
        $lianlianpayConfig['key'] = '201408071000001539_sahdisa_20141205';

        //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

        //版本号
        $lianlianpayConfig['version'] = '1.1';
        //防钓鱼ip
        $lianlianpayConfig['userreq_ip'] = '10.10.246.110';
        //证件类型
        $lianlianpayConfig['id_type'] = '0';
        //订单有效时间  分钟为单位，默认为10080分钟（7天） 
        $lianlianpayConfig['valid_order'] ="30";
        //字符编码格式 目前支持 gbk 或 utf-8
        $lianlianpayConfig['input_charset'] = strtolower('utf-8');
        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $lianlianpayConfig['transport'] = 'http';

        return $lianlianpayConfig;
    }

}
