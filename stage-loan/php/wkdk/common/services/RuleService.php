<?php
namespace common\services;
use common\models\RiskRuleModel;
use common\models\UserIdentityCardModel;

use common\models\UserMobileReportModel;
use common\models\UserModel;
use Yii;

class RuleService
{

    const PATTERN_SCORE = 'score'; // 评分模式
    const PATTERN_RESULT = 'result'; // 结果模式

    const RESULT_PASS = 'pass';
    const RESULT_NOPASS = 'nopass';
    const RESULT_VALID = 'valid';
    
    const OPERATOR_ACCORDANT = 'accordant'; // 一致
    const OPERATOR_DISACCORD = 'disaccord'; // 不一致

    const STATE_DISABLE = 1; // 禁用
    const STATE_ENABLE = 2; // 启用

    const OUTCOME_PASS = 1; // 结果-通过
    const OUTCOME_NO_PASS = 2; // 结果-不通过
    const OUTCOME_NEED_CHECK = 3; // 结果-需人工审核

    const SYMBOL_INCREASE = 'increase'; // 增加
    const SYMBOL_DECREASE = 'decrease'; // 减少


    private $item = ''; // 风控项
    private $pattern = ''; // 模式 result:结果模式 score:评分模式
    private $operator = ''; // 操作符 >,<,>=,<=,==,!==,<=AND<=,accordant,disaccord
    private $val = ''; // 值
    private $outcome = ''; // 结果
    private $symbol = ''; // 增加:increase 减少:decrease
    private $score = ''; // 分数
    private $userId = 0;
    private $rule = null;
    private $query_value = ''; // 查询记录值

    public $totalScore = 0;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    // 身份证验证
    private function idCardVerify()
    {
        $identityCard = UserIdentityCardModel::getIdentityCard($this->userId);
        if ($identityCard) {
            $this->query_value = $identityCard->state;
            $this->excute();
        }
    }

    // 手机3要素 idcard_match
    private function mobileThreeElement()
    {
        $mobileReport = UserMobileReportModel::findLastSuccessMobileReportByUserId($this->userId);
        if ($mobileReport) {
            $this->query_value = $mobileReport->idcard_match;
            $this->excute();
        }
    }

    // 互通号码数
    private function totalNumberOfExchange()
    {
        $mobileReport = UserMobileReportModel::findLastSuccessMobileReportByUserId($this->userId);
        if ($mobileReport) {
            $this->query_value = $mobileReport->interflow_contact_cnt;
            $this->excute();
        }
    }
    // 反欺诈分
    private function antiFraudScore()
    {

    }
    // 夜间通话平均时长
    private function meanTimeOfNightCall()
    {
        $mobileReport = UserMobileReportModel::findLastSuccessMobileReportByUserId($this->userId);
        if ($mobileReport) {
            $this->query_value = $mobileReport->night_avg_call_time;
            $this->excute();
        }
    }
    // 夜晚通话次数
    private function NumberOfCallsAtNight()
    {
        $mobileReport = UserMobileReportModel::findLastSuccessMobileReportByUserId($this->userId);
        if ($mobileReport) {
            $this->query_value = $mobileReport->night_call_cnt;
            $this->excute();
        }
    }
    //实名制信息
    private function realNameSystem()
    {
        $mobileReport = UserMobileReportModel::findLastSuccessMobileReportByUserId($this->userId);
        if ($mobileReport) {
            $mobileReport->report = json_decode($mobileReport->report);
            foreach ($mobileReport->report->basicInfoCheck ?? [] as $key => $value) {
                // 手机号是否实名认证
                if ($value->item == 'mobile_check') {
                    $this->query_value = $value->result;
                    break;
                }
            }
            $this->excute();
        }
    }
    // 手机静默次数
    private function cellPhoneSilence()
    {
        $mobileReport = UserMobileReportModel::findLastSuccessMobileReportByUserId($this->userId);
        if ($mobileReport) {
            $this->query_value = $mobileReport->silence_cnt;
            $this->excute();
        }
    }
    // 日均通话次数
    private function averageDailyCalls()
    {
        $mobileReport = UserMobileReportModel::findLastSuccessMobileReportByUserId($this->userId);
        if ($mobileReport) {
            $this->query_value = $mobileReport->avg_call_cnt;
            $this->excute();
        }
    }
    // 日均通话时长
    private function averageDailyCallLength()
    {
        $mobileReport = UserMobileReportModel::findLastSuccessMobileReportByUserId($this->userId);
        if ($mobileReport) {
            $this->query_value = $mobileReport->avg_call_time;
            $this->excute();
        }
    }
    // 月均消费
    private function monthlyAverageConsumption()
    {
        $mobileReport = UserMobileReportModel::findLastSuccessMobileReportByUserId($this->userId);
        if ($mobileReport) {
            $this->query_value = $mobileReport->avg_fee_month;
            $this->excute();
        }
    }
    // 网龄
    private function netAge()
    {
        $mobileReport = UserMobileReportModel::findLastSuccessMobileReportByUserId($this->userId);
        if ($mobileReport) {
            $this->query_value = (time() - strtotime($mobileReport->reg_time)) / 3600 / 24 / 30;
            $this->excute();
        }

    }
    // 联系人号码总数
    private function totalNumberOfContacts()
    {
        $mobileReport = UserMobileReportModel::findLastSuccessMobileReportByUserId($this->userId);
        if ($mobileReport) {
            $this->query_value = $mobileReport->contact_num_cnt;
            $this->excute();
        }
    }
    // 芝麻信用分
    private function zhimaScore()
    {

    }
    // 话费余额
    private function telephoneBalance()
    {
        $mobileReport = UserMobileReportModel::findLastSuccessMobileReportByUserId($this->userId);
        if ($mobileReport) {
            $mobileReport->content = json_decode($mobileReport->content);
            $this->query_value = $mobileReport->content->basicInfo->amount ?? '';
            $this->excute();
        }
    }
    // 银行卡四要素
    private function bankFourElement()
    {
        $user= UserModel::getAuthState($this->userId);
        if ($user) {
            $this->query_value = $user->is_bankcard_auth;
            $this->excute();
        }
    }
    // 风险清单
    private function riskList()
    {
        $mobileReport = UserMobileReportModel::findLastSuccessMobileReportByUserId($this->userId);
        if ($mobileReport) {
            $this->query_value = $mobileReport->risk_list_cnt;
            $this->excute();
        }
    }

    private function excute()
    {
        $this->pattern = $this->rule->pattern;
        $this->operator = $this->rule->operator;
        $this->symbol = $this->rule->symbol;
        $this->score = $this->rule->score;
        $this->val = $this->rule->val;
        if ($this->pattern == self::PATTERN_SCORE) {
            $this->excuteScore($this->query_value);
        } else if ($this->pattern == self::PATTERN_RESULT) {
            $this->excuteResult($this->query_value);
        }
    }

    private function excuteScore($value)
    {
        switch ($this->operator) {
            case '>': {
                if ($value > $this->val) {
                    $this->caculate();
                }
                break;
            }
            case '<': {
                if ($value < $this->val) {
                    $this->caculate();
                }
                break;
            }
            case '>=': {
                if ($value >= $this->val) {
                    $this->caculate();
                }
                break;
            }
            case '<=': {
                if ($value <= $this->val) {
                    $this->caculate();
                }
                break;
            }
            case '==': {
                if ($value == $this->val) {
                    $this->caculate();
                }
                break;
            }
            case '!==': {
                if ($value != $this->val) {
                    $this->caculate();
                }
                break;
            }
            case '<=AND<=': {
                if ($value >= explode(',', $this->val)[0] && $value <= explode(',', $this->val)[1]) {
                    $this->caculate();
                }
                break;
            }
            default:break;
        }
    }

    private function excuteResult($value)
    {
        // 规则需求为一致
        if ($this->operator == self::OPERATOR_ACCORDANT) {
            // 实际结果为一致
            if ($value == self::RESULT_PASS || $value == self::RESULT_VALID) {
                $this->caculate();
            }
            // 身份证与运营商匹配、手机号是否实名认证
            if ($value == UserMobileReportModel::INCARD_FUZZY_MATCH_SUCCESS || $value == UserMobileReportModel::INCARD_MATCH_SUCCESS) {
                $this->caculate();
            }
        } else if ($this->operator == self::OPERATOR_DISACCORD) {  // 规则需求不一致
            // 实际结果不一致
            if ($value == self::RESULT_NOPASS) {
                $this->caculate();
            }
            // 身份证与运营商不匹配、手机号是否实名认证
            if ($value == UserMobileReportModel::INCARD_MATCH_UNKNOWN || $value == UserMobileReportModel::INCARD_MATCH_FAILURE) {
                $this->caculate();
            }
        }
    }

    private function caculate()
    {
        // 增加
        if ($this->symbol == self::SYMBOL_INCREASE) {
             $this->totalScore = (integer)$this->totalScore + (integer)$this->score;
        } else if ($this->symbol == self::SYMBOL_DECREASE) { // 减少
            $this->totalScore = (integer)$this->totalScore - (integer)$this->score;
        }
    }

    public function getTotalScore()
    {
        $riskRules = RiskRuleModel::getRiskRule();
        foreach($riskRules as $k => $v) {
            // 该条规则禁用
            if ($v->state == self::STATE_DISABLE) {
                break;
            }
            if (method_exists($this, $v->item)) {
                $this->rule = $v;
                $method = $this->item = $v->item;
                $this->$method();
            }
        }
        return  $this->totalScore;
    }
}