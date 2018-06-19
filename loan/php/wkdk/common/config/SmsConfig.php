<?php

namespace common\config;

class SmsConfig
{

    // 验证码错误
    const MOBILE_CODE_ERR = '验证码不正确或已过期（有效期为15分钟）';
    // 动态密码错误（仅登录或注册使用,其它使用短信验证码的地方,使用 MOBILE_CODE_ERR）
    const MOBILE_PASS_ERR = '动态密码错误';
    // 系统异常,DB保存失败的情况
    const MOBILE_CODE_EXP = '发送短信失败，可能系统繁忙，请稍后再试';
    // 短信发送成功
    const MOBILE_CODE_SUCCESS = '发送短信成功，请查收';
    // 发送次数限制: 同一手机号同一模板,1小时内最多可发送7次
    const MOBILE_CODE_LIMIT = '短信发送过于频繁，1分钟后再试';

}