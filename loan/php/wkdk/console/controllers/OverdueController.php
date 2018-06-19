<?php
/**
 * 逾期管理控制器.
 * User: yanqiaowen
 * Date: 2017/11/22
 * Time: 13:43
 */
namespace console\controllers;

use common\models\OverdueLogModel;
use common\models\ProductModel;
use yii\console\Controller;
use common\models\Loan;
use common\models\LoanModel;
use common\bases\CommonService;
use common\models\MobileLogModel;
use common\extend\sms\MsgTemplate;
use common\extend\sms\AlidayuSms;

use Yii;
use yii\helpers\Json;

class OverdueController extends Controller
{
    /**
     * 逾期处理
     */
    public function actionProcessOverdueLoan()
    {
        Yii::info('系统执行逾期检查@' . date('Y-m-d H:i:s'), 'overdue');
        $today = date('Y-m-d');
        $overdueLoans = Loan::find()->with('user')->where(['state' => LoanModel::STATE_REPAYING])->andWhere(['<', 'planned_repayment_at', $today])->all();
        if ($overdueLoans) {
            $service = new CommonService();
            $type = MobileLogModel::TYPE_OVERDUE_NOTICE; // 逾期通知
            
            foreach ($overdueLoans as $overdueLoan) {
                LoanModel::setOverDueStateById($overdueLoan->id); // 设置为逾期
                $lendAt = date('Y-m-d',strtotime($overdueLoan->lending_at)); // 借款日期
                $plannedRepaymentAt = $overdueLoan->planned_repayment_at;// 预计还款日期
                $overdueDays = (strtotime($today) - strtotime($plannedRepaymentAt)) / (3600 * 24); // 逾期天数
                $params = ['date' => $lendAt, 'cash_account' => $overdueLoan->quota, 'day' => $overdueDays];
                $service->sendSmsCode(AlidayuSms::Sign, MsgTemplate::OVERDUE_NOTIFY, $overdueLoan->user->mobile, $params, MobileLogModel::TYPE_OVERDUE_NOTICE); // 逾期通知

                $im = Yii::$app->companyim;
                $im->sendTextToChatGroup($im::SENDER_XIAOBAI, $im::COMPANY_CHAT_ID, sprintf('系统执行逾期操作！设置借款ID(%s)为逾期, 用户ID(%s),金额(%s)！', $overdueLoan->id, $overdueLoan->user->id, $overdueLoan->quota));
            }
        }
    }

    /**
     * 逾期发送短信提醒
     */
    public function actionOverdueLoanNotify()
    {
        Yii::info('系统执行逾期发送短信提醒@' . date('Y-m-d H:i:s'), 'overdue');
        $today = date('Y-m-d');
        $overdueLoans = Loan::find()->with('user')->where(['state' => LoanModel::STATE_OVERDUE])->all();
        if ($overdueLoans) {
            $service = new CommonService();
            $type = MobileLogModel::TYPE_OVERDUE_NOTICE; // 逾期通知
            $product = ProductModel::getActiveProduct();

            foreach ($overdueLoans as $overdueLoan) {
                $lendAt = date('Y-m-d',strtotime($overdueLoan->lending_at)); // 借款日期
                $plannedRepaymentAt = $overdueLoan->planned_repayment_at;// 预计还款日期
                $overdueDays = (strtotime($today) - strtotime( $plannedRepaymentAt)) / (3600 * 24); // 逾期天数

                if ($product && ($overdueDays > $product->limit_overdue_days)) { // 逾期天数大于产品最大逾期上限
                    $overdueFees = $overdueLoan->quota * $product->limit_overdue_days * $overdueLoan->overdue_rate;
                } else {
                    $overdueFees = $overdueLoan->quota * $overdueDays * $overdueLoan->overdue_rate;
                }

                $params = ['date' => $lendAt, 'cash_account' => $overdueLoan->quota, 'day' => $overdueDays];

                // 已经存在逾期日志，更新，没有则添加逾期日志
                $overdueLog = OverdueLogModel::findOneByLoanId($overdueLoan->id);
                if ($overdueLog) {
                    OverdueLogModel::update($overdueLoan->id, $overdueFees, $overdueDays);
                } else {
                    OverdueLogModel::add(['loan_id' => $overdueLoan->id, 'overdue_fees' => $overdueFees, 'overdue_days' => $overdueDays]);
                }
                $service->sendSmsCode(AlidayuSms::Sign, MsgTemplate::OVERDUE_NOTIFY, $overdueLoan->user->mobile, $params, MobileLogModel::TYPE_OVERDUE_NOTICE); // 逾期通知
            }
        }
    }
}