<?php
namespace common\models;

use common\bases\CommonModel;
use Yii;

class PayLogModel extends CommonModel
{
    const STATE_PENDING = 'pending'; // 等待处理
    const STATE_SUCCESS = 'success'; // 交易成功
    const STATE_WAITING = 'waiting'; // 等待支付
    const STATE_PROCESSING = 'processing'; // 付款处理中
    const STATE_APPLY = 'apply'; // 付款申请
    const STATE_CHECK = 'check'; // 复核申请
    const STATE_CANCEL = 'cancel'; // 付款退款（付款成功后，有可能发生退款）
    const STATE_FAILURE = 'failure'; // 付款失败
    const STATE_CLOSED = 'closed'; // 付款关闭
    const STATE_REFUND = 'refund'; // 支付退款（认证支付）


    const TYPE_AUTHPAY = 1; // 支付类型：1-用户主动还款（认证支付）
    const TYPE_REALTIMEPAY = 2; // 支付类型：2-放款（实时支付）
    const TYPE_INSTALLMENTPAY = 3; // 支付类型：3-平台代扣（分期支付）
    /**
     * 通过商户唯一订单号查询支付交易信息
     * @param string $noOrder 商户唯一订单号
     * @return object
     */
    public static function findPayLogByNoOrder($noOrder)
    {
       return PayLog::find()->where(['no_order' => $noOrder])->one();
    }

    /**
     * 根据交易记录Id修改交易信息
     * @param integer $id 交易记录ID
     * @param array $params
     * @return boolean true-保存成功 false-保存失败
     */
    public static function updatePayLogById($id, $params)
    {
        $payLog = PayLog::findOne(['id' => $id]);

        if (isset($params['state'])) { $payLog->state =  $params['state']; } // 更改状态
        if (isset($params['oid_paybill'])) { $payLog->oid_paybill =  $params['oid_paybill']; } // 连连支付支付单号
        if (isset($params['info_order'])) { $payLog->info_order =  $params['info_order']; } // 订单描述
        if (isset($params['settle_date'])) { $payLog->settle_date =  $params['settle_date']; } // 清算日期
        if (isset($params['bank_code'])) { $payLog->bank_code =  $params['bank_code']; } // 银行编号
        if (isset($params['card_no'])) { $payLog->card_no =  $params['card_no']; } // 银行卡号
        if (isset($params['no_agree'])) { $payLog->no_agree =  $params['no_agree']; } // 银通签约的协议编号
        $payLog->updated_at = date('Y-m-d H:i:s'); // 更新时间

        if($payLog->validate()) { // 验证rules
            if (!$payLog->save()) return false;
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取用户的有效订单(主动还款/放款)
     * @param integer $userId 用户ID
     * @param integer $loan_id 借款ID
     * @param integer $pay_type 支付类型 默认值【认证支付（主动还款）】
     * @return object|boolean 返回查询的结果
     */
    public static function findActivePayLog($userId, $loan_id, $pay_type = self::TYPE_AUTHPAY)
    {
        return PayLog::find()->where(['user_id' => $userId])
                             ->andWhere(['loan_id'=>$loan_id])
                             ->andWhere(['pay_type'=> $pay_type])
                             ->orderBy('id desc')
                             ->one(); // 获取用户最新的支付记录详情
    }

    /**
     * 添加支付记录
     * @param array $params 字段的键值信息（key=>value）
     * @return boolean true-保存成功  false-保存失败
     */
    public static function addPayLog($params)
    {
        $payLog = new PayLog();
        $payLog->user_id = $params['user_id'];
        if(isset($params['loan_id'])) { $payLog->loan_id = $params['loan_id']; }
        $payLog->pay_type = $params['pay_type'];
        $payLog->no_order = $params['no_order'];
        $payLog->info_order = $params['info_order'];
        $payLog->money_order = (float)$params['money_order'];
        if(isset($params['plan_detail'])) { $payLog->plan_detail = $params['plan_detail']; }
        $payLog->created_at = $params['dt_order'];
        if (isset($params['plan_id'])) {
            $payLog->plan_id = $params['plan_id'];
        }
        if ($payLog ->validate()) {
            if (!$payLog->save()) return false;
            return true;
        }
        return false;
    }

    /**
     * 通过商户唯一订单号更改支付记录
     * @param string $noOrder 商户唯一订单流水号
     * @param array $params 参数
     * @return bool|integer 返回该支付记录对应的借款ID
     */
    public static function updatePayLogByNoOrder($noOrder, $params)
    {
        $payLog = PayLog::findOne(['no_order' => $noOrder]);
        if ($payLog) {
            if (isset($params['oid_paybill'])) { $payLog->oid_paybill = $params['oid_paybill']; } // 连连支付支付单号
            if (isset($params['state'])) { $payLog->state = $params['state']; } // 状态
            if (isset($params['confirm_code'])) { $payLog->confirm_code = $params['confirm_code']; } // 验证码
            if (isset($params['settle_date'])) { $payLog->settle_date = $params['settle_date']; } // 账务日期
            if (isset($params['info_order'])) { $payLog->info_order = $params['info_order']; } // 订单描述

            if (isset($params['money_order'])) { $payLog->money_order = $params['money_order']; } // 订单交易金额

            $payLog->updated_at = date('Y-m-d H:i:s');
            if (!$payLog->save())  return false;
            return $payLog->loan_id;
        }
        return false;
    }
    /**
     * 查询所有待确认的放款（实时支付）支付记录
     * @param integer $offset 查询的偏移量
     * @param integer $limit 查询的记录数目
     * @return array 返回查询的结果
     */
    public static function findAllNeedConfirmPayLog($offset = 0, $limit = 20)
    {
        return PayLog::find()->with('user')
            ->joinWith('userBank')
            ->where(['pay_type' => self::TYPE_REALTIMEPAY])
            ->andWhere(['pay_log.state' => self::STATE_CHECK])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }

    /**
     * 按ID查询支付记录
     * @param integer $payLogId 支付记录ID
     * @return object|null 返回查询的记录
     */
    public static function findPayLogById($payLogId)
    {
        return PayLog::find()->where(['id' => $payLogId])->one();
    }

    /**
     * 按用户ID 查询有效的认证支付订单
     * @param integer $userId 用户ID
     * @return array|null 返回查询的结果
     */
    public static function findActiveAuthPayLogByUserId($userId)
    {
        return PayLog::find()->where(['user_id' => $userId])
                             ->andWhere(['pay_type' => self::TYPE_AUTHPAY])
                             ->andWhere(['state' => self::STATE_PENDING])
                             ->orderBy('id desc')
                             ->one();
    }

    /**
     * 按用户ID 查询用户还款记录（还款成功、还款失败）
     * @param integer $userId 用户ID
     * @param integer $offset 查询的偏移量
     * @param integer $limit 查询的记录数
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findRepaymentPayLogByUserId($userId, $offset, $limit)
    {
        // 条件：1、状态为 success<还款成功> 或 （ pending状态 且 时间超过2个小时的<还款失败>）
        return PayLog::find()->with('loan')
                             ->with('budgetPlan')
                             ->where(['or', 'state=\''. self::STATE_SUCCESS . '\'', 'state=\'' . self::STATE_PENDING . '\' and created_at<\'' . date('Y-m-d H:i:s', time() - 3600 * 2) . '\''])
                             ->andWhere(['user_id' => $userId])
                             ->andWhere(['pay_type' => PayLogModel::TYPE_AUTHPAY])
                             ->offset($offset)
                             ->limit($limit)
                             ->all();
    }
}