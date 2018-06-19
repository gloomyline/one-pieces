<?php
namespace common\services;
use common\services\CurlService;

use Yii;

class TencentService
{
    const URL = 'csec.api.qcloud.com/v2/index.php';
    const SECRET_ID = 'AKIDvgCUYPJOXR7bruAHanmnrgHdkdIinrZ4';
    const SECRET_KEY = 'OYT9bnlV0HJ7g06xZw4waz7zc081e2B9';

    public static function sendRequest($url, $method = 'POST')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (false !== strpos($url, "https")) {
            // 证书
            // curl_setopt($ch,CURLOPT_CAINFO,"ca.crt");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $resultStr = curl_exec($ch);
        $result = json_decode($resultStr, true);

        return $result;
    }

    public static function makeURL($method, $action, $region, $secretId, $secretKey, $args)
    {
        $args['Nonce'] = (string)rand(0, 0x7fffffff);
        $args['Action'] = $action;
        $args['Region'] = $region;
        $args['SecretId'] = $secretId;
        $args['Timestamp'] = (string)time();

        /* Sort by key (ASCII order, ascending), then calculate signature using HMAC-SHA1 algorithm */
        ksort($args);
        $args['Signature'] = base64_encode(hash_hmac('sha1', $method . self::URL . '?' . self::makeQueryString($args, false), $secretKey, true)
        );

        $url = 'https://' . self::URL . '?' . self::makeQueryString($args, true);
        return $url;
    }

    public static function makeQueryString($args, $isURLEncoded)
    {
        $arr = array();
        foreach ($args as $key => $value) {
            if (!$isURLEncoded) {
                $arr[] = "$key=$value";
            } else {
                $arr[] = $key . '=' . urlencode($value);
            }
        }
        return implode('&', $arr);
    }
}