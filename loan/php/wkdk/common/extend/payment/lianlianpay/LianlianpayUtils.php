<?php

namespace common\extend\payment\lianlianpay;

use Yii;
use yii\helpers\Html;

class LianlianpayUtils
{
    const LOG_CATEGORY = 'lianlianpay';
    /**
     * RSA签名
     * $data签名数据(需要先排序，然后拼接)
     * 签名用商户私钥，必须是没有经过pkcs8转换的私钥
     * 最后的签名，需要用base64编码
     * return Sign签名
     */

    public static function Rsasign($data, $privateKeyPath)
    {
        $priKey = @file_get_contents($privateKeyPath);

        //转换为openssl密钥，必须是没有经过pkcs8转换的私钥
        $res = openssl_get_privatekey($priKey);

        //调用openssl内置签名方法，生成签名$sign
        openssl_sign($data, $sign, $res, OPENSSL_ALGO_MD5);

        //释放资源
        openssl_free_key($res);
        
        //base64编码
        $sign = base64_encode($sign);
        //file_put_contents("log.txt","签名原串:".$data."\n", FILE_APPEND);
        return $sign;
    }

    /********************************************************************************/

    /**RSA验签
     * $data待签名数据(需要先排序，然后拼接)
     * $sign需要验签的签名,需要base64_decode解码
     * 验签用连连支付公钥
     * return 验签是否通过 bool值
     */
    public static function Rsaverify($data, $sign, $llPayPublicKeyPath)
    {
        //读取连连支付公钥文件
        $pubKey = file_get_contents($llPayPublicKeyPath);

        //转换为openssl格式密钥
        $res = openssl_get_publickey($pubKey);

        //调用openssl内置方法验签，返回bool值
        $result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_MD5);
        
        //释放资源
        openssl_free_key($res);

        //返回资源是否成功
        return $result;
    }

    /**
     * MD5验证签名
     * @param $prestr 需要签名的字符串
     * @param $sign 签名结果
     * @param $key 私钥
     * return 签名结果
     */
    public static function md5Verify($prestr, $sign, $key)
    {
        $prestr = $prestr ."&key=". $key;
        $mysgin = md5($prestr);
        if($mysgin == $sign) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    public static function createLinkstring($para)
    {
        $arg  = "";
        while (list ($key, $val) = each ($para)) {
            $arg.=$key."=".$val."&";
        }
        //去掉最后一个&字符
        $arg = substr($arg,0,count($arg)-2);
        //file_put_contents("log.txt","转义前:".$arg."\n", FILE_APPEND);
        //如果存在转义字符，那么去掉转义
        if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
        //file_put_contents("log.txt","转义后:".$arg."\n", FILE_APPEND);
        return $arg;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    public static function createLinkstringUrlencode($para)
    {
        $arg  = "";
        while (list ($key, $val) = each ($para)) {
            $arg.=$key."=".urlencode($val)."&";
        }
        //去掉最后一个&字符
        $arg = substr($arg,0,count($arg)-2);
        
        //如果存在转义字符，那么去掉转义
        if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
        
        return $arg;
    }

    /**
     * 除去数组中的空值和签名参数
     * @param $para 签名参数组
     * return 去掉空值与签名参数后的新签名参数组
     */
    public static function paraFilter($para)
    {
        $para_filter = array();
        while (list ($key, $val) = each ($para)) {
            if($key == "sign" || $val == "") {
                continue;
            } else {
                $para_filter[$key] = $para[$key];
            }
        }
        return $para_filter;
    }

    /**
     * 对数组排序
     * @param $para 排序前的数组
     * return 排序后的数组
     */
    public static function argSort($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }

    /**
     * 远程获取数据，POST模式
     * 注意：
     * @param $url 指定URL完整路径地址
     * @param $para 请求的数据
     * return 远程输出的数据
     */
    public static function getHttpResponseJSON($url, $para)
    {
        $json = json_encode($para);     
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //信任任何证书
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");                          
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(                 
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json))         
        );
        $responseText = curl_exec($curl);
        Yii::info("log.txt","返回值:".$responseText."\n", self::LOG_CATEGORY);
        curl_close($curl);
        return $responseText;
    }

    /**
     * 远程获取数据，GET模式
     * 注意：
     * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
     * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
     * @param $url 指定URL完整路径地址
     * @param $cacert_url 指定当前工作目录绝对路径
     * return 远程输出的数据
     */
    public static function getHttpResponseGET($url,$cacert_url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
        curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
        $responseText = curl_exec($curl);
        //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($curl);
        
        return $responseText;
    }


    /**
     * 连连支付接口相关log
     * 
     * @param $logText log内容
     * @return 排序后的数组
     */
    public static function lianlianpayLog($logText)
    {
        Yii::info($logText, self::LOG_CATEGORY);
    }
    public function aesEncrypt($data,$key,$nonce)
    {
        return base64_encode( openssl_encrypt ($data, "AES-256-CTR", $key, true, $nonce . "\0\0\0\0\0\0\0\1"));
    }
    public function rsaEncrypt($data,$publicKey)
    {
        openssl_public_encrypt ( $data, $encrypted, $publicKey, OPENSSL_PKCS1_OAEP_PADDING ); // 公钥加密
        return base64_encode ( $encrypted );
    }
    public function genLetterDigitRandom($size)
    {
        $allLetterDigit = array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
        $randomSb = "";
        $digitSize = count($allLetterDigit)-1;
        for($i = 0; $i < $size; $i ++){
            $randomSb .= $allLetterDigit[rand(0,$digitSize)];
        }
        return $randomSb;
    }
    public function lianlianpayEncrypt($req, $publicKey, $hmackKey, $version, $aesKey, $nonce)
    {
        $B64hmackKey = $this->rsaEncrypt ( $hmackKey, $publicKey );
        $B64aesKey = $this->rsaEncrypt ( $aesKey, $publicKey );
        $B64nonce = base64_encode($nonce);
        $encry = $this->aesEncrypt ( utf8_decode($req), $aesKey, $nonce);
        $message = $B64nonce . "$" .$encry;
        $sign = hex2bin(hash_hmac("sha256",$message,$hmackKey));
        $B64sign = base64_encode($sign);
        return $version . '$' . $B64hmackKey . '$' . $B64aesKey . '$' . $B64nonce . '$' . $encry . '$' . $B64sign;
    }
    /**
     * 连连请求参数 json 报文加密
     * @param string $plaintext 待加密的json信息
     * @param string $public_key RSA公钥路径
     * @return mixed 加密后的信息
     */
    public function ll_encrypt($plaintext, $publicKeyPath)
    {
        $publicKey = @file_get_contents($publicKeyPath);
        $puKey = openssl_pkey_get_public ( $publicKey );
        $hmackKey = $this->genLetterDigitRandom(32);
        $version = "lianpay1_0_1";
        $aesKey = $this->genLetterDigitRandom(32);
        $nonce = $this->genLetterDigitRandom(8);
        return $this->lianlianpayEncrypt($plaintext, $puKey, $hmackKey, $version, $aesKey, $nonce);
    }
}