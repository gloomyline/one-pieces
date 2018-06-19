<?php

namespace common\models;

use common\bases\CommonModel;

class LoanLogModel extends CommonModel
{
    const TITLE_SUBMIT_SUCCESS = 'submit_success'; // 提交资料成功
    const TITLE_AUDITING = 'auditing'; // 审核中
    const TITLE_AUDIT_SUCCESS = 'audit_success'; // 审核通过
    const TITLE_AUDIT_FAILURE = 'audit_failure'; // 审核未通过
    const TITLE_CONFIRMING= 'confirming'; // 商家确认中
    const TITLE_CONFIRM_SUCCESS = 'confirm_success'; // 商家确认通过
    const TITLE_CONFIRM_FAILURE = 'confirm_failure'; // 商家确认未通过
    const TITLE_GRANTING = 'granting'; // 放款中
    const TITLE_GRANT_FAILURE = 'grant_failure'; // 放款失败
    const TITLE_GRANT_SUCCESS = 'grant_success'; // 放款成功
    const TITLE_FINISHED = 'finished'; // 已还完
    /**
     * 添加借款订单日志
     * @param string $loanId 借款订单ID
     * @param int $title 标题
     * @param int $content 内容
     * @return boolean
     */
    public static function addLoanLog($loanId, $title, $content)
    {
        $model = new LoanLog();
        $model->loan_id = $loanId;
        $model->title = $title;
        $model->content = $content;
        $model->created_at = date('Y-m-d H:i:s');

        return $model->save();
    }

    public static function getLoanLogByState($loanId, $state = 'all')
    {
        $model = LoanLog::find();
        $model->Where(['loan_id' => $loanId]);
        switch ($state) {
            case 'submit' :
                $model->andWhere(['title' => LoanLogModel::TITLE_SUBMIT_SUCCESS]);break;
            case 'audit':
                $model->andWhere(['or', 'title=\''. LoanLogModel::TITLE_AUDITING . '\'', 'title=\'' . LoanLogModel::TITLE_AUDIT_SUCCESS . '\' or title=\'' . LoanLogModel::TITLE_AUDIT_FAILURE . '\'']);
                break;
            case 'confirm':
                $model->andWhere(['or', 'title=\''. LoanLogModel::TITLE_CONFIRMING . '\'', 'title=\'' . LoanLogModel::TITLE_CONFIRM_SUCCESS . '\' or title=\'' . LoanLogModel::TITLE_CONFIRM_FAILURE . '\'']);
                break;
            case 'loan':
                $model->andWhere(['or', 'title=\''. LoanLogModel::TITLE_GRANTING . '\'', 'title=\'' . LoanLogModel::TITLE_GRANT_FAILURE . '\' or title=\'' . LoanLogModel::TITLE_GRANT_SUCCESS . '\' or title=\'' . LoanLogModel::TITLE_FINISHED . '\'']);
                break;
            default:break;
        }
        $model->orderBy('id desc');
        return $model->one();
    }

    /**
     * 删除指定的借款记录
     * @param integer $loanId 借款ID
     * @param string $title 标题
     * @return integer|boolean 受影响的条数
     */
    public static function delAppointedLoanLog($loanId, $title = 'repaying')
    {
        return LoanLog::deleteAll('loan_id = :loanId AND title = :title', [':loanId' => $loanId, ':title' => $title]);
    }

}