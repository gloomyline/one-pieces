<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=rm-bp10j1adr4hp480q6.mysql.rds.aliyuncs.com;dbname=wkdk',
            'username' => 'yanqiaowen',
            'password' => 'PyzYw#NJGr@',
            'charset' => 'utf8',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'sms' => [
            'class' => 'common\extend\sms\AlidayuSms',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'companyim' => [
            'class' => 'common\extend\im\DingTalkIm',
            'enable' => true,
            'notice_configs' => [
                'sys_error' => [
                    'sender' => 'e21dedb189b2012453c6e9e6bbd10ea5543c4a724bfe5523b025191d70d8c6b9',
                    'atMobiles' => [
                        '13666056816', // 颜巧文
                        '17679982795', // 王俊伟
                        '18859686211', // 黎志辉
                        '15240017969', // 汪瑞峰
                    ],
                    'isAtAll' => false,
                ],
                'pay_warn' => [
                    'sender' => 'e21dedb189b2012453c6e9e6bbd10ea5543c4a724bfe5523b025191d70d8c6b9',
                    'atMobiles' => [],
                    'isAtAll' => true,
                ],
                'repay_warn' => [
                    'sender' => 'e21dedb189b2012453c6e9e6bbd10ea5543c4a724bfe5523b025191d70d8c6b9',
                    'atMobiles' => [],
                    'isAtAll' => true,
                ],
            ],
        ],
        'redis' => [
            'class' => 'common\extend\redis\RedisClient',
            'hostname' => 'r-bp122b102dca4fd4360.redis.rds.aliyuncs.com',
            'port' => 6379,
            'database' => 2,
            'password' => 'Wkdk1007',
            'enable_stats' => true,
        ],
    ],
];