<?php

namespace common\models;

use common\bases\CommonModel;

class MobileLogModel extends CommonModel
{
    const TYPE_AUTHENTICATION_CODE = 'auth_code'; // 短信验证码
    const TYPE_LOAN_NOTICE = 'loan'; // 放款通知
    const TYPE_REPAMENT_NOTICE = 'repayment'; // 还款通知
    const TYPE_OVERDUE_NOTICE = 'overdue'; // 逾期通知
    const TYPE_REPAYMENT_SUCCESS_NOTICE = 'repay_succ'; // 还款成功
    const TYPE_WITHDRAWAL_SUCCESS_NOTICE = 'withdraw'; // 提现成功
    const TYPE_OVERDUE_MASS = 'overdue_mass'; // 逾期群发通知

    /**
     * 获取短信记录
     * @param integer $offset 查询的基准数
     * @param integer $limit 查询的记录数
     * @param string $mobile 手机号
     * @param string $type 短信类型
     * @param integer $state 发送状态
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getMobileLogList($offset, $limit, $mobile, $type, $state)
    {
        $model = MobileLog::find()
            ->with('user');
        if ($mobile !='') {
            $model->andWhere(['mobile' => $mobile]);
        }
        if ($type !='') {
            $model->andWhere(['type' => $type]);
        }
        if ($state !='') {
            if ($state == 1) { // 成功
                $model->andWhere(['like', 'return_message' ,'"Message":"OK"']);
            } elseif ($state == 2) {
                $model->andWhere(['not like', 'return_message' ,'"Message":"OK"']);
            }
        }
        return [
            'count' => $model->count(),
            'result' => $model->orderBy(['id' => SORT_DESC])->offset($offset)->limit($limit)->all()
        ];
    }

    /**
     * 添加 发送信息日志
     * @param string $mobile 手机号码
     * @param string $mess 返回信息
     * @param string $code 验证码
     * @param string $content 发送内容
     * @param string $type 类型 auth_code:短信验证码 loan:放款通知 repayment:还款通知 overdue:逾期通知 repay_succ:还款成功 withdrawal:提现成功  overdue_mass：逾期群发通知
     * @param integer $loanId 借款id
     */
    public static function addList($mobile,$mess,$code,$content,$type,$loanId = null)
    {
        $logModel = new MobileLog();
        $logModel->mobile = $mobile;
        $logModel->created_at = date('Y-m-d H:i:s');
        $logModel->return_message = $mess;
        $logModel->code = $code;
        $logModel->send_message = $content;
        $logModel->type = $type;
        if (!empty($loanId)) {
            $logModel->loan_id = $loanId;
        }
        
        $logModel->save();
    }

    /**
     * 查找是否存在该借款订单的最新逾期群发通知记录
     * @param $loanId 借款订单id
     * @param $mobile 接收短信的手机号码
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findOverdueMassByLoanIdAndMobile($loanId, $mobile)
    {
        return MobileLog::find()->where(['type' => self::TYPE_OVERDUE_MASS, 'loan_id' => $loanId, 'mobile' => $mobile])->orderBy(['id' => SORT_DESC])->one();
    }


}