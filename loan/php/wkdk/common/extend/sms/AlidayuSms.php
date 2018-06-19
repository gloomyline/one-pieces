<?php

namespace common\extend\sms;

use Yii;
use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;

class AlidayuSms {
    const AccessKeyID = 'LTAIWRDShMkQ6JZx';
    const AccessKeySecret = '5F4g4LOi1mDkPYmdeAu8zwxG1cFNai';
    const Sign = '万匹思科技';

    /**
     * 取得阿里大鱼的短信模板ID
     * @return string 模板ID
     */
    public static function templateMsg()
    {
        return [
            // 你好，你正在注册成为新用户，验证码是${code}，切勿告知他人。
            MsgTemplate::SIGN_UP => 'SMS_94095026', // 注册验证码

            // 尊敬的用户，您本次验证码是：${code}（有效期15分钟），请勿将验证码转告他人，如非本人操作，请忽略本短信。
            MsgTemplate::FORGET_PASSWORD => 'SMS_100815107', // 忘记密码验证码
            
            // 尊敬的用户：您于${date}申请的${cash_account}元将于${arrive_date}到期，应还金额${account}元。如已付款，无需理会。
            MsgTemplate::REPAYMENT_NOTIFY => 'SMS_100820101', // 还款通知

            // 尊敬的用户：您于${time}已成功付款，付款金额${account}元。本次已结清。
            MsgTemplate::REPAYMENT_SUCCESS => 'SMS_100855107', // 还款成功通知

            // 尊敬的用户：您于${date}申请的${cash_account}元已成功到账，到账金额${account}元，请您注意查收！
            MsgTemplate::LOAN_NOTIFY => 'SMS_100755093', // 放款通知

            // 尊敬的用户：很抱歉，您的本次申请未到账。请等待系统重新处理，如有疑问，请您咨询客服电话0592-5767737.
            MsgTemplate::LOAN_FAILURE => 'SMS_107090002', // 放款失败通知

            // 尊敬的用户：您于${date}申请的${cash_account}元，已过期${day}天，请尽快通过平台进行操作处理。如已付款，无需理会。
            MsgTemplate::OVERDUE_NOTIFY => 'SMS_100780100', // 逾期通知

            // ${name}于${date}申请的${account}元，已过期${day}天，请转告其尽快通过平台进行操作处理。
            MsgTemplate::OVERDUE_MASS => 'SMS_114385740', // 逾期群发
        ];
    }

    /**
     * 构造器
     *
     */
    public function __construct()
    {
        Config::load();

        // 短信API产品名
        $product = "Dysmsapi";

        // 短信API产品域名
        $domain = "dysmsapi.aliyuncs.com";

        // 暂时不支持多Region
        $region = "cn-hangzhou";

        // 服务结点
        $endPointName = "cn-hangzhou";

        // 初始化用户Profile实例
        $profile = DefaultProfile::getProfile($region, self::AccessKeyID, self::AccessKeySecret);

        // 增加服务结点
        DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

        // 初始化AcsClient用于发起请求
        $this->acsClient = new DefaultAcsClient($profile);
    }

    /**
     * 发送短信范例
     *
     * @param string $signName <p>
     * 必填, 短信签名，应严格"签名名称"填写，参考：<a href="https://dysms.console.aliyun.com/dysms.htm#/sign">短信签名页</a>
     * </p>
     * @param string $templateCode <p>
     * 必填, 短信模板Code，应严格按"模板CODE"填写, 参考：<a href="https://dysms.console.aliyun.com/dysms.htm#/template">短信模板页</a>
     * (e.g. SMS_0001)
     * </p>
     * @param string $phoneNumbers 必填, 短信接收号码 (e.g. 12345678901)
     * @param array|null $templateParam <p>
     * 选填, 假如模板中存在变量需要替换则为必填项 (e.g. Array("code"=>"12345", "product"=>"阿里通信"))
     * </p>
     * @param string|null $outId [optional] 选填, 发送短信流水号 (e.g. 1234)
     * @return stdClass
     */
    public function sendSms($signName, $templateCode, $phoneNumbers, $templateParam = null, $outId = null)
    {

        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        // 必填，设置雉短信接收号码
        $request->setPhoneNumbers($phoneNumbers);

        // 必填，设置签名名称
        $request->setSignName($signName);

        // 必填，设置模板CODE
        $request->setTemplateCode($templateCode);

        // 可选，设置模板参数
        if($templateParam) {
            $request->setTemplateParam(json_encode($templateParam));
        }

        // 可选，设置流水号
        if($outId) {
            $request->setOutId($outId);
        }

        // 发起访问请求
        $acsResponse = $this->acsClient->getAcsResponse($request);
        // 打印请求结果
        // var_dump($acsResponse);

        return $acsResponse;

    }
}
