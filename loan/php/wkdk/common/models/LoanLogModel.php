<?php

namespace common\models;

use common\bases\CommonModel;

class LoanLogModel extends CommonModel
{
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

    public static function getLoanLog($loanId)
    {
        $model = LoanLog::find();
        $model->Where(['loan_id' => $loanId]);
        $model->orderBy('id desc');
        return $model->all();
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