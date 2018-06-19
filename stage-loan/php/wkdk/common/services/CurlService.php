<?php
namespace common\services;

use Yii;

class CurlService
{
    /**
     * 获取远程数据
     * @param unknown $url
     * @param string $data
     * @return Ambigous <string, mixed>
     */
    public static function sendRequest($url, $data = '')
    {
        $_SERVER['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'] ?? "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11";
        $contents   = '' ;
        $url        = htmlspecialchars_decode($url) ;
    
        if (function_exists('curl_init') && @$ch = curl_init()) {
            // 获取远程文件内容
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
            curl_setopt($ch, CURLOPT_TIMEOUT , 60);
            curl_setopt($ch, CURLOPT_SSLVERSION, 0) ; // 设定SSL版本
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            $contents = curl_exec($ch);
            curl_close($ch);
        }
    
        if (empty( $contents)) {
            if (!empty($data)) {
                $context_param['http'] = array( 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'method' => 'POST', 'content' => $data);
            } else {
                $context_param['http'] = array( 'user_agent' => $_SERVER['HTTP_USER_AGENT']);
            } 
            $context = stream_context_create($context_param);
            @$contents = file_get_contents($url, 0, $context);
        }
        return $contents;
    }

    public static function sendRequestByJson()
    {

    }
}