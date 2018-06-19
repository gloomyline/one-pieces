<?php

namespace common\extend\sms;

abstract class MsgTemplate {

    // ### 用户帐号相关
    //【用户注册】 你好，你正在注册成为新用户，验证码是${code}，切勿告知他人。
    const SIGN_UP = 'SIGN_UP';
    //【忘记密码】 尊敬的用户，您本次验证码是：${code}（有效期15分钟），请勿将验证码转告他人，如非本人操作，请忽略本短信。
    const FORGET_PASSWORD = 'FORGET_PASSWORD';
    // 尊敬的用户：您于${date}申请的${cash_account}元将于${arrive_date}到期，应还金额${account}元。如已付款，无需理会。
    const REPAYMENT_NOTIFY = 'REPAYMENT_NOTIFY';
    // 尊敬的用户：您于${time}已成功付款，付款金额${account}元。本次已结清。
    const REPAYMENT_SUCCESS = 'REPAYMENT_SUCCESS';
    // 尊敬的用户：您于${date}申请的${cash_account}元已成功到账，到账金额${account}元，请您注意查收！
    const LOAN_NOTIFY = 'LOAN_NOTIFY';
    // 尊敬的用户：很抱歉，您的本次申请未到账。请等待系统重新处理，如有疑问，请您咨询客服电话0592-5767737.
    const LOAN_FAILURE = 'LOAN_FAILURE';
    // 尊敬的用户：您于${date}申请的${cash_account}元，已过期${day}天，请尽快通过平台进行操作处理。如已付款，无需理会。
    const OVERDUE_NOTIFY = 'OVERDUE_NOTIFY';
    // ${name}于${date}申请的${account}元，已过期${day}天，请转告其尽快通过平台进行操作处理。
    const OVERDUE_MASS = 'OVERDUE_MASS';

}
