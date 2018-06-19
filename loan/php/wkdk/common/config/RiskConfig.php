<?php

namespace common\config;

class RiskConfig
{

    public static $risks = [
        'CertificationCenter' => [
            'title' => 'Certification center',
            'description' => '认证中心',
            'enable' => true,
            'items' => [
                'idCardVerify' => '身份证验证',
                'bankFourElement' => '银行卡四要素',
                'mobileThreeElement' => '手机三要素',
            ],
        ],
        'ZhimaCredit' => [
            'title' => 'Zhima credit',
            'description' => '芝麻信用',
            'enable' => true,
            'items' => [
                'zhimaScore' => '芝麻信用分',
                'antiFraudScore' => '反欺诈分',
                'industryConcernList' => '行业关注名单',
            ],
        ],
        'OperatorBasicInformation' => [
            'title' => 'Operator basic information',
            'description' => '运营商基本信息',
            'enable' => true,
            'items' => [
                'telephoneBalance' => '话费余额',
                'realNameSystem' => '实名制信息',
                'netAge' => '网龄',
            ],
        ],
        'RiskCase' => [
            'title' => 'Risk case',
            'description' => '风险情况',
            'enable' => true,
            'items' => [
                'riskList' => '风险清单',
                'overdueCredit' => '信贷逾期',
                'bullLending' => '多头借贷',
                'riskCall' => '风险通话',
            ],
        ],
        'SocialSituation' => [
            'title' => 'Social situation',
            'description' => '社交情况',
            'enable' => true,
            'items' => [
                'mostFrequentContactArea' => '最经常联系区域',
                'totalNumberOfContacts' => '联系人号码总数',
                'totalNumberOfExchange' => '互通号码总数',
            ],
        ],
        'CallConditions' => [
            'title' => 'Call conditions',
            'description' => '通话请况',
            'enable' => true,
            'items' => [
                'averageDailyCalls' => '日均通话数',
                'averageDailyCallLength' => '日均通话时长',
                'cellPhoneSilence' => '手机静默次数',
                'NumberOfCallsAtNight' => '夜间通话次数',
                'meanTimeOfNightCall' => '夜间通话平均时长',
            ],
        ],
        'Consumption' => [
            'title' => 'consumption',
            'description' => '消费情况',
            'enable' => true,
            'items' => [
                'monthlyAverageConsumption' => '月均消费',
            ],
        ],
        'UserInformation' => [
            'title' => 'User information',
            'description' => '用户信息',
            'enable' => true,
            'items' => [
                'education' => '学历',
                'age' => '年龄',
                'gender' => '性别',
                'career' => '职业',
                'live_address' => '居住地址',
                'regist_time' => '注册时间',
                'emergencyLinkman' => '紧急联系人',
                'liveLength' => '居住时长',
                'idCarAddress' => '身份证地址',
                'isBlankList' => '是否黑名单',
                'userStatus' => '用户状态',
            ],
        ],
        'BorrowingInformation' => [
            'title' => 'Borrowing information',
            'description' => '借款信息',
            'enable' => true,
            'items' => [
                'totalBorrowingTimes' => '借款总次数',
                'totalRepaymentTimes' => '还款总次数',
                'timesOfLoanFailure' => '借款不通过次数',
                'timesOfOverdue' => '逾期次数',
                'overdueForMoreThanThirtyDays' => '借款人有逾期30天以上',
            ],
        ],
    ];

}
