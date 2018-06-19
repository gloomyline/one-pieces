<?php
namespace common\services;
use Yii;
class RedisService
{
    const SETTLED_PREFIX = 'settled_'; // 商家入驻前缀 设置key值前缀
    const SHOP_PREFIX = 'shop_'; // 商家锁

    /**
     * 获取key值
     * @param string $key 键名称
     * @return string|boolean 返回键对应的值，无键值时返回false
     */
    public static function getKey($key)
    {
        if (Yii::$app->redis->exists($key)) {
            return Yii::$app->redis->get($key);
        }
        return false;
    }

    /**
     * 设置key值
     * @param string $key 键名称
     * @param string|array|object $value 对应键的值
     */
    public static function setKey($key, $value)
    {
        Yii::$app->redis->set($key, $value); // 设置key值
    }

    /**
     * 设置key值
     * @param string $key 键名称
     * @param string|array|object $value 对应键的值
     * @param integer $expireSeconds 过期时间（单位秒）
     */
    public static function setKeyWithExpire($key, $value, $expireSeconds)
    {
        Yii::$app->redis->set($key, $value); // 设置key值
        Yii::$app->redis->expire($key, $expireSeconds); // 设置key值过期时间
    }

    /**
     * 设置指定键过期时间
     * @param string $key 需要设置过期的键名
     * @param integer $expireSeconds 过期时间（单位秒）注：为0或负数时，删除key值
     */
    public static function setExpire($key, $expireSeconds)
    {
        Yii::$app->redis->expire($key, $expireSeconds); //设置key过期
    }

    /**
     * 删除指定的键值
     * @param string $key 键名称
     */
    public static function delKey($key)
    {
        Yii::$app->redis->del($key); //删除key
    }
}