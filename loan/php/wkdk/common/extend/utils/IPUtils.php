<?php

namespace common\extend\utils;

class IPUtils 
{
    public static function getUserIP ()
    {
        if (!empty($_SERVER['HTTP_X_WZJ_REAL_IP'])) {
            $realIp = trim($_SERVER['HTTP_X_WZJ_REAL_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) || !empty($_SERVER['HTTP_SLB_ID'])) {
            $ips = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
            $realIp = trim(end($ips));
        } else {
            $realIp = $_SERVER['REMOTE_ADDR'];
        }
        return $realIp;
    }
}