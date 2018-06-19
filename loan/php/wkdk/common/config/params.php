<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
    'limu_callback_url' => 'http://api.wkdk.cn/1/lm-callback',

    //'llpay_notify_url' => "http://m.wkdk.cn/llpay-notify",
    'llpay_sign_return_url' => "http://m.wkdk.cn/llpay-sign-return", // 连连支付银行卡签约回调地址
    'llpay_payment_notify' => 'http://api.wkdk.cn/1/llpay-payment-notify', // 连连支付实时付款异步通知

    'llpay_authpay_notify' => 'http://api.wkdk.cn/1/llpay-auth-pay-notify', // 连连支付认证支付异步通知（用户主动还款）
    'llpay_authpay_url_return' => 'http://m.wkdk.cn/payment/lianlian-auth-pay-url-return', // 连连支付认证支付回显地址
    'llpay_authpay_back_url' => '', // 左上角返回按钮，指定返回地址 不传默认 history.go(-1)

    'llpay_bankcard_repayment_notify' => 'http://api.wkdk.cn/1/llpay-bankcard-repayment-notify', // 连连支付分期支付异步通知（银行卡还款代扣）


    'zhimaCreditAppId' => '1004436', // 芝麻信用 应用标识
    'zhimaCharset' => 'UTF-8', // 加密加签时使用的charset
    'zhimaPrivateKeyFilePath' => dirname(__FILE__).'\..\extend\zmop\rsa_private_key.pem', // rsa加密 私有秘钥 文件路径
    'zhimaPublicKeyFilePath' => dirname(__FILE__).'\..\extend\zmop\zhima_rsa_public_key.pem', // rsa加密 公有秘钥 文件路径
    'zhimaGatewayUrl' => 'https://zmopenapi.zmxy.com.cn/openapi.do', // 芝麻信用网关地址
    'zhimaPlatform' => 'zmop', // 芝麻信用来源平台
    'zhimaProductCode' => 'w1010100100000000001', // 芝麻信用分数产品码

];
