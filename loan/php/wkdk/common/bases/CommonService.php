<?php

namespace common\bases;
use common\models\MobileLogModel;
use yii\base\Component;
use Yii;
use common\extend\sms\AlidayuSms;
use common\config\SmsConfig;
use common\models\MobileCodeModel;
/**
 * 服务类基类
 */
class CommonService extends Component
{
    /**
     * 字符串转数组
     *
     * @param string $string 字符串
     * @param string $delimiter 分隔符
     * @return array
     */
    public function stringToArray($string, $delimiter = ",") 
    {
        if (strstr($string, $delimiter)) {
            $arr = explode($delimiter, $string);
        } else {
            $arr = [$string];
        }
        return $arr;
    }

    /**
     * 发送短信验证
     *
     * @param string $mobile 手机号
     * @param string $msgTemplate 短信模板
     * @param string $type 短信的类型
     * @param integer $loanId 借款id 催收时逾期群发通知需赋值
     * @return array
     */
    public function sendSmsCode($sign, $msgTemplate, $mobile, $param, $type, $loanId = null)
    {
        if (empty($mobile) or ! preg_match("/^(1[0-9]{10},*)+$/", $mobile)) {
            return ['code' => 2000, 'message' => '请输入可用的手机号码'];
        }
        if (isset($param['code']) && ! empty($param['code'])) {
            if (!MobileCodeModel::addMobileCode($mobile, $param['code'])) {
                return ['code' => 2000, 'message' => SmsConfig::MOBILE_CODE_EXP];
            }
        }
        $alidayuSms = AlidayuSms::TemplateMsg();
        $result = \Yii::$app->sms->sendSms($sign, $alidayuSms[$msgTemplate], $mobile, $param);
        $mobileArr = self::stringToArray($mobile);
        foreach ($mobileArr as $v) {
            $logModel = MobileLogModel::addList(
                $v,
                json_encode($result),
                json_encode($param),
                $alidayuSms[$msgTemplate],
                $type,
                $loanId
            );
        }

        if (isset($result->sub_code) && $result->sub_code == 'isv.BUSINESS_LIMIT_CONTROL') {
            return ['code' => 2000, 'message' => SmsConfig::MOBILE_CODE_LIMIT];
        } elseif (isset($result->code)) {
            return ['code' => 2000, 'message' => SmsConfig::MOBILE_CODE_EXP];
        }
        return ['code' => 1000, 'message' => SmsConfig::MOBILE_CODE_SUCCESS];

    }

    /**
     * 发送短信验证
     *
     * @param string $mobile 手机号
     * @param string $msgTemplate 短信模板
     * @param array $param 短信模板参数
     * @param string $type 短信的类型
     * @param integer $loanId 借款id 催收时逾期群发通知需赋值
     * @return array
     */
    public function sendPhoneCode($mobile, $msgTemplate, $param, $type, $loanId = null)
    {
        if (empty($mobile) or ! preg_match("/^1[0-9]{10}$/", $mobile)) {
            return ['code' => 2000, 'message' => '请输入可用的手机号码'];
        }
        if (isset($param['code']) and ! empty($param['code'])) {
            if (!MobileCodeModel::addMobileCode($mobile, $param['code'])) {
                return ['code' => 2000, 'message' => SmsConfig::MOBILE_CODE_EXP];
            }
        }
        $result = \Yii::$app->sms->sendPhoneMsg(
            $mobile,
            $msgTemplate,
            json_encode(preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $param))
        );
        $alidayuSms = AlidayuSms::TemplateMsg();
        $logModel = MobileLogModel::addList(
            $mobile,
            json_encode($result),
            json_encode($param),
            $alidayuSms[$msgTemplate],
            $type,
            $loanId
        );
        if (isset($result->sub_code) and $result->sub_code == 'isv.BUSINESS_LIMIT_CONTROL') {
            return ['code' => 2000, 'message' => SmsConfig::MOBILE_CODE_LIMIT];
        } elseif (isset($result->code)) {
            return ['code' => 2000, 'message' => SmsConfig::MOBILE_CODE_EXP];
        }
        return ['code' => 1000, 'message' => SmsConfig::MOBILE_CODE_SUCCESS];

    }
}