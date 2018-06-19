<?php

namespace common\extend\sms;

abstract class MsgTemplate {

    // ### 用户帐号相关
    //【用户注册】 你好，你正在注册成为新用户，验证码是${code}，切勿告知他人。
    const SIGN_UP = 'SIGN_UP';
    //【忘记密码】 尊敬的用户，您本次验证码是：${code}（有效期15分钟），请勿将验证码转告他人，如非本人操作，请忽略本短信。
    const FORGET_PASSWORD = 'FORGET_PASSWORD';
    // 尊敬的用户：您的第${yearmonth}期账单金额为${account}元，请于${date}前付款。如已付款，无需理会。
    const REPAYMENT_NOTIFY = 'REPAYMENT_NOTIFY';
    // 尊敬的用户：您${yearmonth}账单已成功付款，付款金额${account}元。本次已结清。
    const REPAYMENT_SUCCESS = 'REPAYMENT_SUCCESS';
    // 尊敬的用户：您于${date}申请的总期数${periods}金额${cash}元,已成功到账，到账金额${account}元，请您注意查收！
    const LOAN_NOTIFY = 'LOAN_NOTIFY';
    // 尊敬的用户：很抱歉，您的本次申请未到账。请等待系统重新处理，如有疑问，请您咨询客服电话0592-5767737.
    const LOAN_FAILURE = 'LOAN_FAILURE';
    // 尊敬的用户：您${yearmonth}账单金额为${account}元，已过期${day}天，请尽快通过平台进行操作处理。如已付款，无需理会。
    const OVERDUE_NOTIFY = 'OVERDUE_NOTIFY';
    // ${name}的${yearmonth}应付金额为${account}，已过期${day}天，请转告其尽快进行操作处理。
    const OVERDUE_MASS = 'OVERDUE_MASS';
    // ${name}于${date}申请的${account}元，已通过系统审核，请尽快确认订单
    const SHOP_ORDER_CONFIRM = 'SHOP_ORDER_CONFIRM';
    // 尊敬的用户：您于${date}申请的${periods}期，金额为${cash}元，已给商户转账，转账金额${account}元，敬请知悉！
    const LOAN_NOTIFY_USER = 'LOAN_NOTIFY_USER';
    // ${name}于${date}购买商品的购买金额为${account}元已经发放到您的账户，请注意查收。
    const LOAN_NOTIFY_SHOP = 'LOAN_NOTIFY_SHOP';

}
