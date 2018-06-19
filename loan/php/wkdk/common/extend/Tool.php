<?php
namespace common\extend;
use common\models\MobileLog;
use yii\helpers\Json;

class Tool
{
    /**
      * 切分字符串
      * @date 2017-09-0
      * @auther: addition
      * @param unknowtype
      * @return return_type
      */
    public static function expReturnKey($value,$key=-1,$delimiter=':::',$ifnull='')
    {
         $v = explode($delimiter, $value);
         if ($key == -1) {
             return $v;
         } else {
             if (!isset($v[$key])) {
                 return $ifnull;
             } else {
                 return $v[$key];
             }
         }
    }

    /**
     * 生成字母数字随机组合数
     * 例如随机生成 5 位 字母和数字组合 getRandomString(2);
     * @auther: addition
     * @param $len
     * @param null $chars
     * @return string
     */
    public static function getRandomString($len, $chars=null)
    {
        if (is_null($chars)) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        }
        mt_srand(10000000*(double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++) {
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }
    
    public static function getRandomStringNoNum($len, $chars=null)
    {
        if (is_null($chars)) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }
        mt_srand(10000000*(double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++) {
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }
    
    /**
      * 生成随机字母
      * @date 2017-09-05
      * @auther: addition
      * @param unknowtype
      * @return return_type
      */
    public static function getRandomNum($len, $chars=null)
    {
        if (is_null($chars)) {
            $chars = "0123456789";
        }
        
        for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++) {
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }

    /**
     * 过滤emoji
     * @param $str
     * @return mixed
     */
    public static function filterEmoji($str)
    {
        $str = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);

        return $str;
    }

    // 判断字符串 $str 是否以 $needle 开头
    public static function startWith($str, $needle)
    {
        return strpos($str, $needle) === 0;
    }

}