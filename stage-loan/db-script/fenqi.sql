CREATE TABLE `admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '' COMMENT '管理员名称',
  `password` varchar(64) NOT NULL DEFAULT '' COMMENT '密码',
  `real_name` varchar(10) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `login_ip` char(15) NOT NULL DEFAULT '' COMMENT '登入IP',
  `login_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '登入时间',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='管理员';

CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='权限';

CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='角色';

CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='权限分配';


CREATE TABLE `user_mobile_report` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `token` varchar(64) NOT NULL DEFAULT '' COMMENT '立木token',
  `report` longtext COMMENT '运营商报告内容',
  `content` longtext COMMENT '运营商报告原始数据内容',
  `state` varchar(10) NOT NULL DEFAULT 'busy' COMMENT '状态 pass：认证通过 nopass：认证不通过 busy：待认证',
  `has_report` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已生成报告 0:未生成 1:已生成',
  `reg_time` timestamp NULL DEFAULT NULL COMMENT '入网时间',
  `idcard_match` int(3) NOT NULL DEFAULT '3' COMMENT '身份证号与运营商数据是否匹配：3-未知 2-模糊匹配成功 1-匹配成功 0-匹配失败',
  `name_match` int(3) NOT NULL DEFAULT '3' COMMENT '姓名与运营商数据是否匹配：3-未知 2-模糊匹配成功 1-匹配成功 0-匹配失败',
  `risk_list_cnt` int(10) NOT NULL DEFAULT '0' COMMENT '命中风险清单次数',
  `overdue_loan_cnt` int(10) NOT NULL DEFAULT '0' COMMENT '信贷逾期次数',
  `multi_lend_cnt` int(10) NOT NULL DEFAULT '0' COMMENT '多头借贷次数',
  `risk_call_cnt` int(10) NOT NULL DEFAULT '0' COMMENT '风险通话次数',
  `fre_contact_area` varchar(20) NOT NULL DEFAULT '' COMMENT '最常联系人区域',
  `contact_num_cnt` int(10) NOT NULL DEFAULT '0' COMMENT '联系人号码总数',
  `interflow_contact_cnt` int(10) NOT NULL DEFAULT '0' COMMENT '互通号码数',
  `avg_call_cnt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '日均通话次数',
  `avg_call_time` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '日均通话时长（m）',
  `silence_cnt` int(10) NOT NULL DEFAULT '0' COMMENT '静默次数',
  `night_call_cnt` int(10) NOT NULL DEFAULT '0' COMMENT '夜间通话次数',
  `night_avg_call_time` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '夜间平均通话时长',
  `avg_fee_month` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '月均消费',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='手机运营商报告';

CREATE TABLE `user_limu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `token` varchar(64) NOT NULL DEFAULT '' COMMENT '立木token',
  `content` longtext COMMENT '查询内容',
  `platform_type` int(4) NOT NULL DEFAULT '0' COMMENT '平台类型：1-京东 2-淘宝 3-学信网 4-信用卡账单 5-网银流水',
  `state` varchar(10) NOT NULL DEFAULT 'busy' COMMENT '状态 pass：认证通过 nopass：认证不通过 busy：待认证',
  `has_report` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已生成报告 0:未生成 1:已生成',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_token` (`token`),
  KEY `idx_mobile_platform_type` (`mobile`,`platform_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户立木征信信息表';

CREATE TABLE `user_bank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `bank_name` varchar(60) NOT NULL DEFAULT '' COMMENT '银行名称',
  `bank_no` varchar(32) NOT NULL DEFAULT '' COMMENT '银行卡号',
  `bank_user` varchar(30) NOT NULL DEFAULT '' COMMENT '银行卡姓名',
  `bank_code` varchar(10) NOT NULL DEFAULT '' COMMENT '银行编码',
  `card_type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '银行卡类型 2-储蓄卡 3-信用卡',
  `agreeno` char(16) NOT NULL DEFAULT '' COMMENT '连连支付签约协议号',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否默认使用的卡 0:不是 1:是',
  `state` varchar(10) NOT NULL DEFAULT 'invalid' COMMENT '状态 valid:有效 invalid:无效',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id_bank_no` (`user_id`,`bank_no`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户银行帐户';

CREATE TABLE `user_additional` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `industry` varchar(20) NOT NULL DEFAULT '' COMMENT '从事行业',
  `position` varchar(20) NOT NULL DEFAULT '' COMMENT '工作岗位',
  `company_name` varchar(30) NOT NULL DEFAULT '' COMMENT '单位名称',
  `company_area` varchar(30) NOT NULL DEFAULT '' COMMENT '单位所在地',
  `company_addr` varchar(50) NOT NULL DEFAULT '' COMMENT '单位详细地址',
  `company_tel` varchar(12) NOT NULL DEFAULT '' COMMENT '单位电话',
  `linkman_relation_fir` varchar(4) NOT NULL DEFAULT '' COMMENT '1号联系人与本人关系',
  `linkman_name_fir` varchar(10) NOT NULL DEFAULT '' COMMENT '1号联系人姓名',
  `linkman_tel_fir` varchar(11) NOT NULL DEFAULT '' COMMENT '1号联系人手机号码',
  `linkman_relation_sec` varchar(4) NOT NULL DEFAULT '' COMMENT '2号联系人与本人关系',
  `linkman_name_sec` varchar(10) NOT NULL DEFAULT '' COMMENT '2号联系人姓名',
  `linkman_tel_sec` varchar(11) NOT NULL DEFAULT '' COMMENT '2号联系人手机号码',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户其他信息表（可用于补充用户信息，如公司信息、人际关系等）';

CREATE TABLE `user_basic` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `live_area` varchar(30) NOT NULL DEFAULT '' COMMENT '居住区域',
  `live_addr` varchar(50) NOT NULL DEFAULT '' COMMENT '详细地址',
  `live_time` varchar(10) NOT NULL DEFAULT '' COMMENT '居住时长',
  `is_work_auth` tinyint(4) NOT NULL DEFAULT '0' COMMENT '工作信息认证 0:未填写/未认证 1：已认证',
  `is_relation_auth` tinyint(4) NOT NULL DEFAULT '0' COMMENT '人际关系认证 0:未填写/未认证 1：已认证',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户信息（可用于判断用户是否完善信息）';

CREATE TABLE `article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '文章标题',
  `type` varchar(10) NOT NULL DEFAULT '' COMMENT '分类 acitivity:活动中心 problem:常见问题',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1显示 2不显示',
  `image` varchar(100) NOT NULL DEFAULT '' COMMENT '图片',
  `sort` int(4) NOT NULL DEFAULT '0' COMMENT '排序',
  `content` text NOT NULL COMMENT ' 内容',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章表';

CREATE TABLE `product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(10) NOT NULL DEFAULT '' COMMENT '产品名称',
  `categories` int(2) NOT NULL DEFAULT '1' COMMENT '产品分类 1：现金分期 2：消费分期',
  `max_quota` int(10) NOT NULL DEFAULT '0' COMMENT '借款最大额度',
  `min_quota` int(10) NOT NULL DEFAULT '0' COMMENT '借款最小额度',
  `periods` varchar(30) NOT NULL DEFAULT '' COMMENT '借款期数',
  `annualized_interest_rate` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '年化利率',
  `repayment_way` tinyint(1) NOT NULL DEFAULT '1' COMMENT '还款方式 1:等本等息',
  `active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '上线状态 0:关闭 1:开启',
  `trial_rate` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '信审费率',
  `service_rate` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '服务费率',
  `overdue_rate` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '逾期费率',
  `poundage` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '手续费率',
  `prepayment_poundage` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '提前还款手续费率',
  `prepayment_poundage_max` int(10) NOT NULL DEFAULT '0' COMMENT '提前还款手续费上限',
  `limit_overdue_days` int(10) NOT NULL DEFAULT '180' COMMENT '逾期最大天数限制',
  `use` varchar(150) NOT NULL DEFAULT '' COMMENT '用途',
  `admin_id` int(10) NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品';

CREATE TABLE `loan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `encoding` varchar(32) DEFAULT '' COMMENT '订单编号',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `quota` int(10) NOT NULL DEFAULT '0' COMMENT '申请额度',
  `period` smallint(3) NOT NULL DEFAULT '0' COMMENT '申请期限',
  `repayment_way` tinyint(1) NOT NULL DEFAULT '1' COMMENT '还款方式 1:到期本息',
  `check_at` timestamp NULL DEFAULT NULL COMMENT '初审时间',
  `review_at` timestamp NULL DEFAULT NULL COMMENT '复审时间',
  `lending_at` timestamp NULL DEFAULT NULL COMMENT '放款时间',
  `repayment_at` timestamp NULL DEFAULT NULL COMMENT '实际还款时间',
  `planned_repayment_at` date DEFAULT NULL COMMENT '计划还款时间',
  `state` varchar(20) NOT NULL DEFAULT 'auditing' COMMENT '借款状态',
  `preliminary_officer` int(10) NOT NULL DEFAULT '0' COMMENT '初审人员',
  `preliminary_opinion` varchar(200) NOT NULL DEFAULT '' COMMENT '初审意见',
  `review_officer` int(10) NOT NULL DEFAULT '0' COMMENT '复审人员',
  `review_opinion` varchar(200) NOT NULL DEFAULT '' COMMENT '复审意见',
  `arrival_amount` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '到账额度',
  `repayed_amount` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '用户已还款金额',
  `interest` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '借款息费',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `annualized_interest_rate` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '年化利率',
  `trial_rate` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '信审费率',
  `service_rate` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '服务费率',
  `overdue_rate` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '逾期费率',
  `poundage` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '手续费率',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `loan_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '借款订单ID',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '标题',
  `content` varchar(200) NOT NULL DEFAULT '' COMMENT '内容',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_loan_id` (`loan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mobile_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号码',
  `code` varchar(8) NOT NULL DEFAULT '' COMMENT '验证码',
  `created_at` int(11) DEFAULT '0' COMMENT '添加时间',
  `expire_time` int(11) DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY (`id`),
  KEY `idx_mobile_code_expire_time` (`mobile`,`code`,`expire_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='短信验证码表';

CREATE TABLE `mobile_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(11) DEFAULT '' COMMENT '电话号码',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `type` varchar(13) NOT NULL DEFAULT '' COMMENT '类型 auth_code:短信验证码 loan:放款通知 repayment:还款通知 overdue:逾期通知 repay_succ:还款成功 withdrawal:提现成功 overdue_mass:逾期群发短信',
  `return_message` varchar(255) DEFAULT '' COMMENT '返回信息',
  `code` varchar(255) DEFAULT '' COMMENT '验证码',
  `send_message` varchar(255) DEFAULT '' COMMENT '发送内容',
  `loan_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '借款订单ID',
  PRIMARY KEY (`id`),
  KEY `idx_moblie_loan_id` (`mobile`,`loan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE `overdue_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '借款订单ID',
  `overdue_days` int(10) NOT NULL DEFAULT '0' COMMENT '逾期天数',
  `overdue_fees` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '逾期费用',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_loan_id` (`loan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='逾期借款日志';

CREATE TABLE `pay_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `loan_id` int(10) NOT NULL DEFAULT '0' COMMENT '借款ID',
  `pay_type` int(5) DEFAULT '0' COMMENT '支付类型：1-用户主动还款（认证支付）2-放款（实时支付）3-平台代扣（分期支付）',
  `no_order` varchar(32) NOT NULL DEFAULT '' COMMENT '商户唯一订单号',
  `oid_paybill` varchar(32) DEFAULT '' COMMENT '连连支付支付单号',
  `state` varchar(10) NOT NULL DEFAULT 'pending' COMMENT 'pending:等待处理 success:交易成功 processing:处理中',
  `money_order` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '交易金额',
  `info_order` varchar(255) NOT NULL DEFAULT '' COMMENT '订单描述',
  `settle_date` timestamp NULL DEFAULT NULL COMMENT '清算日期',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `confirm_code` varchar(6) NOT NULL DEFAULT '' COMMENT '验证码',
  PRIMARY KEY (`id`),
  KEY `idx_no_order` (`no_order`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_loan_id` (`loan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付记录';

CREATE TABLE `quota_apply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '申请ID',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `admin_id` int(10) NOT NULL DEFAULT '0' COMMENT '审核员ID',
  `apply_quota` int(10) NOT NULL DEFAULT '0' COMMENT '申请额度',
  `allow_quota` int(10) NOT NULL DEFAULT '0' COMMENT '通过额度',
  `available_quota` int(10) NOT NULL DEFAULT '0' COMMENT '可用额度',
  `total_quota` int(10) NOT NULL DEFAULT '1000' COMMENT '总额度',
  `state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '申请通过状态：0-待审核 1-审核通过 2-审核失败',
  `apply_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '额度类型：0-后台添加 1-用户申请 2-系统添加',
  `memo` varchar(300) NOT NULL DEFAULT '' COMMENT '备注',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间/申请时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='额度申请表';

CREATE TABLE `risk_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item` varchar(100) NOT NULL DEFAULT '' COMMENT '风控字段',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '字段中文名称',
  `module` varchar(50) NOT NULL DEFAULT '' COMMENT '所属风控模块',
  `pattern` varchar(10) NOT NULL DEFAULT '' COMMENT '模式 result:结果模式 score:评分模式',
  `operator` varchar(10) NOT NULL DEFAULT '' COMMENT '操作符',
  `val` varchar(10) NOT NULL DEFAULT '' COMMENT '值',
  `outcome` tinyint(1) NOT NULL DEFAULT '3' COMMENT '1:通过 2:不通过 3:需人工审核',
  `symbol` varchar(10) NOT NULL DEFAULT '' COMMENT '增加:increase 减少:decrease',
  `score` smallint(5) NOT NULL DEFAULT '0' COMMENT '分数',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:禁用, 2:启用',
  `remarks` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `admin_id` int(10) NOT NULL DEFAULT '0' COMMENT '管理员ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='风控规则表';

CREATE TABLE `statistics` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` date DEFAULT NULL COMMENT '日期',
  `user_count` int(10) NOT NULL DEFAULT '0' COMMENT '新增用户数',
  `loan_count` int(10) NOT NULL DEFAULT '0' COMMENT '借款笔数',
  `loan_interest` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '借款息费',
  `loan_user` int(10) NOT NULL DEFAULT '0' COMMENT '借款人数',
  `lend_count` int(10) NOT NULL DEFAULT '0' COMMENT '放款笔数',
  `lend_amount` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '放款金额',
  `lend_user` int(10) NOT NULL DEFAULT '0' COMMENT '放款人数',
  `repayment_amount` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '还款金额',
  `repayment_count` int(10) NOT NULL DEFAULT '0' COMMENT '还款笔数',
  `repayment_overdue_count` int(10) NOT NULL DEFAULT '0' COMMENT '逾期还款笔数',
  `repayment_overdue_amount` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '逾期还款金额',
  `overdue_count` int(10) NOT NULL DEFAULT '0' COMMENT '逾期笔数',
  `overdue_amount` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '逾期金额',
  `overdue_penalty` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '逾期罚息',
  `urge_count` int(10) NOT NULL DEFAULT '0' COMMENT '催收次数',
  `urge_loan_count` int(10) NOT NULL DEFAULT '0' COMMENT '催收笔数',
  `urge_success_count` int(10) NOT NULL DEFAULT '0' COMMENT '催收成功次数',
  `bad_count` int(10) NOT NULL DEFAULT '0' COMMENT '坏账笔数',
  `bad_amount` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '坏账金额',
  PRIMARY KEY (`id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='数据每日统计表';

CREATE TABLE `urge` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '借款订单ID',
  `admin_id` int(10) NOT NULL DEFAULT '0' COMMENT '催收员id',
  `state` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1:等待催收 2:催收未还款 3:催收已还款 4:坏账',
  `urge_count` int(10) NOT NULL DEFAULT '0' COMMENT '催款次数',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_loan_id` (`loan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='逾期催收表';

CREATE TABLE `urge_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `urge_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '催收记录ID',
  `urge_way` tinyint(4) NOT NULL COMMENT '催款方式1:短信 2:电话 3:上门 4:第三方',
  `urge_result` tinyint(4) NOT NULL COMMENT '催款结果 1:客户承诺还款 2:客户无法还款 3:催款成功 4:客户失联 5:坏账',
  `planned_repayment_at` date DEFAULT NULL COMMENT '预计还款时间',
  `content` varchar(200) NOT NULL DEFAULT '' COMMENT '催款情况说明',
  `admin_id` int(10) NOT NULL DEFAULT '0' COMMENT '催收员id',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_urge_id` (`urge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='催还记录';

CREATE TABLE `user` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号码',
  `password` char(64) NOT NULL DEFAULT '' COMMENT '密码',
  `real_name` varchar(10) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `gender` int(2) NOT NULL DEFAULT '0' COMMENT '性别：0-未知 1-男 2-女',
  `age` int(3) NOT NULL DEFAULT '0' COMMENT '年龄',
  `education` varchar(10) NOT NULL DEFAULT '' COMMENT '学历',
  `zm_open_id` varchar(64) NOT NULL DEFAULT '' COMMENT '用户在商户端的身份标识ID。为空时，表示用户芝麻信用未授权',
  `login_ip` varchar(30) NOT NULL DEFAULT '' COMMENT '登入IP',
  `login_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登入时间',
  `auth_key` varchar(32) NOT NULL DEFAULT '' COMMENT 'cookie authkey',
  `success_count` int(10) NOT NULL DEFAULT '0' COMMENT '成功借款次数',
  `success_amount` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '成功借款金额',
  `success_repay_count` int(10) NOT NULL DEFAULT '0' COMMENT '成功还款次数',
  `success_repay_amount` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '成功还款金额',
  `fronzen_quota` int(10) NOT NULL DEFAULT '0' COMMENT '冻结额度',
  `available_quota` int(10) NOT NULL DEFAULT '0' COMMENT '可用额度',
  `total_quota` int(10) NOT NULL DEFAULT '1000' COMMENT '总额度',
  `state` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1:注册未申请 2:正常 3:逾期用户 4:黑名单',
  `is_forbidden` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:启用,2:禁用',
  `is_identity_auth` tinyint(4) NOT NULL DEFAULT '0' COMMENT '身份认证 0:未填写/未认证 1：已认证',
  `is_profile_auth` tinyint(4) NOT NULL DEFAULT '0' COMMENT '个人信息认证 0:未填写/未认证 1：已认证',
  `is_bankcard_auth` tinyint(4) NOT NULL DEFAULT '0' COMMENT '银行卡认证 0:未填写/未认证 1：已认证',
  `is_phone_auth` tinyint(4) NOT NULL DEFAULT '0' COMMENT '手机认证 0:未填写/未认证 1：已认证',
  `is_jd_auth` tinyint(4) NOT NULL DEFAULT '0' COMMENT '京东认证 0:未填写/未认证 1：已认证',
  `is_taobao_auth` tinyint(4) NOT NULL DEFAULT '0' COMMENT '淘宝认证 0:未填写/未认证 1：已认证',
  `is_edu_auth` tinyint(4) NOT NULL DEFAULT '0' COMMENT '学历认证 0:未填写/未认证 1：已认证',
  `is_bill_auth` tinyint(4) NOT NULL DEFAULT '0' COMMENT '信用卡账单认证 0:未填写/未认证 1：已认证',
  `is_ebank_auth` tinyint(4) NOT NULL DEFAULT '0' COMMENT '网银流水认证 0:未填写/未认证 1：已认证',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

CREATE TABLE `user_identity_card` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `real_name` varchar(10) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `identity_no` varchar(30) NOT NULL DEFAULT '' COMMENT '身份证号',
  `state` varchar(10) NOT NULL COMMENT '状态 pass：验证通过 nopass：验证不通过',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户身份证信息';

CREATE TABLE `user_token` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL COMMENT '用户ID',
  `access_token` varchar(128) NOT NULL DEFAULT '' COMMENT 'Token',
  `expiry_timestamp` int(11) NOT NULL COMMENT 'token过期时间',
  `source` varchar(128) NOT NULL DEFAULT '' COMMENT '来源',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_access_token` (`access_token`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='授权Token';

alter table `loan` drop column `repayment_at`;
alter table `loan` drop column `planned_repayment_at`;
alter table `loan` MODIFY `encoding` varchar(32) DEFAULT '' COMMENT '订单编号';
alter table `loan` MODIFY `repayment_way` tinyint(1) NOT NULL DEFAULT '1' COMMENT '还款方式 1:等本等息';

# 分期计划表
create table `budget_plan`(
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `loan_id` int(10) NOT NULL DEFAULT '0' COMMENT '借款ID',
  `term` int(2) NOT NULL DEFAULT '0' COMMENT '期数',
  `monthly` DECIMAL(8,2) NOT NULL DEFAULT '0.00' COMMENT '月供（单位：元）',
  `principal` DECIMAL(8,2) NOT NULL DEFAULT '0.00' COMMENT '本期本金',
  `interest` DECIMAL(8,2) NOT NULL DEFAULT '0.00' COMMENT '本期借款息费',
  `repayed_amount` DECIMAL(8,2) NOT NULL DEFAULT '0.00' COMMENT '本期实际还款金额',
  `state` varchar(20) NOT NULL DEFAULT 'waiting' COMMENT '本期借款状态：waiting-等待中 repaying-还款中 finished-已还完',
  `begin_repayment_at` date DEFAULT NULL COMMENT '开始还款时间',
  `planned_repayment_at` date DEFAULT NULL COMMENT '计划还款时间',
  `repayment_at` timestamp NULL DEFAULT NULL COMMENT '实际还款时间',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '分期计划表';


alter table `user` add is_picture_auth tinyint(4) NOT NULL DEFAULT '0' COMMENT '亲签照认证 0:未填写/未认证 1：已认证' after `is_ebank_auth`;

CREATE TABLE `category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(10) NOT NULL DEFAULT '' COMMENT '分类名称',
  `parent_id` int(10) NOT NULL DEFAULT '0' COMMENT '上级分类id，默认0为顶级分类',
  `sort` int(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示0：不显示1：显示',
  `description` varchar(200) NOT NULL DEFAULT '' COMMENT '分类描述',
  `admin_id` int(10) NOT NULL DEFAULT '0' COMMENT '管理员id',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品分类';

#修改产品表产分类，和借款期数字段名
ALTER TABLE `product` CHANGE `categories` `type` int(2) NOT NULL DEFAULT '1' COMMENT '产品分类 1：现金分期 2：消费分期';
ALTER TABLE `product` CHANGE `periods` `period` varchar(30) NOT NULL DEFAULT '' COMMENT '借款期数';

# loan表 添加字段 借款分类、用途
alter table `loan` add `type` int(2) NOT NULL DEFAULT '1' COMMENT '借款分类 1：现金分期 2：消费分期' after `user_id`;
alter table `loan` add `use` varchar(20) NOT NULL DEFAULT '' COMMENT '用途';

# 更改默认总额度 和可用额度
alter table `user` modify `available_quota` int(10) NOT NULL DEFAULT '10000' COMMENT '可用额度' AFTER `fronzen_quota`;
alter table `user` modify `total_quota` int(10) NOT NULL DEFAULT '10000' COMMENT '总额度' AFTER `available_quota`;

# 添加分期计划状态 逾期中
alter table `budget_plan` MODIFY `state` varchar(20) NOT NULL DEFAULT 'waiting' COMMENT '本期借款状态：waiting-等待中 repaying-还款中 overdue-逾期中 finished-已还完' after `repayed_amount`;

# 分期计划表添加新字段  借款息费明细
alter table `budget_plan` add `trial_fee` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '信审费用' after `interest`;
alter table `budget_plan` add `service_fee` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '服务费用' after `trial_fee`;
alter table `budget_plan` add `poundage_fee` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '手续费用' after `service_fee`;
alter table `budget_plan` add `interest_fee` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '利息' after `poundage_fee`;

CREATE TABLE `shop` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_name` varchar(20) NOT NULL DEFAULT '' COMMENT '商户名称',
  `legal_person_name` varchar(10) NOT NULL DEFAULT '' COMMENT '企业法人姓名',
  `legal_person_mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '企业法人手机号码',
  `legal_person_id_no` varchar(30) NOT NULL DEFAULT '' COMMENT '企业法人身份证号',
  `legal_person_id_card_pic` varchar(200) NOT NULL DEFAULT '' COMMENT '企业法人身份证正反照片',
  `is_eq` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1：法人与实际控制人一致 0：不一致',
  `actual_controller_name` varchar(10) NOT NULL DEFAULT '' COMMENT '实际控制人姓名',
  `actual_controller_mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '实际控制人手机号码',
  `actual_controller_id_no` varchar(30) NOT NULL DEFAULT '' COMMENT '实际控制人身份证号',
  `actual_controller_id_card_pic` varchar(200) NOT NULL DEFAULT '' COMMENT '实际控制人身份证正反照片',
  `corporate_contacts_email` varchar(30) NOT NULL DEFAULT '' COMMENT '企业联系人邮箱',
  `corporate_contacts_mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '企业联系人手机号',
  `bank_name` varchar(60) NOT NULL DEFAULT '' COMMENT '银行名称',
  `bank_no` varchar(32) NOT NULL DEFAULT '' COMMENT '打款银行卡号',
  `shop_addr` varchar(50) NOT NULL DEFAULT '' COMMENT '企业详细地址',
  `three_cards_pic` varchar(300) NOT NULL DEFAULT '' COMMENT '企业三证图片',
  `registered_capital` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '注册资金(万)',
  `office_area` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '办公面积(平方米)',
  `staff_no` int(10) NOT NULL DEFAULT '0' COMMENT '职工人数',
  `corporate_office_pic` varchar(800) NOT NULL DEFAULT '' COMMENT '企业办公场所照片及房产租赁协议',
  `salesman_logo_pic` varchar(100) NOT NULL DEFAULT '' COMMENT '业务员与公司logo合照',
  `qualification_pic` varchar(300) NOT NULL DEFAULT '' COMMENT '相关行业资质照',
  `holder_no_remark` varchar(200) NOT NULL DEFAULT '' COMMENT '持证人数备注',
  `protocol_pic` varchar(100) NOT NULL DEFAULT '' COMMENT '商户合作协议',
  `authorization_pic` varchar(100) NOT NULL DEFAULT '' COMMENT '征信授权协议书',
  `commitment_pic` varchar(100) NOT NULL DEFAULT '' COMMENT '商户承诺书',
  `bank_bill_pic` varchar(5000) NOT NULL DEFAULT '' COMMENT '近六个月网银流水',
  `category` varchar(30) NOT NULL DEFAULT '' COMMENT '商户可分期商品类别',
  `city_id` int(11) NOT NULL DEFAULT '0' COMMENT '商户所在城市ID',
  `salesman_id` int(10) NOT NULL DEFAULT '0' COMMENT '业务经理ID',
  `auditor_id` int(10) NOT NULL DEFAULT '0' COMMENT '审核人员ID',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示0：待审核 1：审核通过 2：审核不通过',
  `audit_updated_at` timestamp NULL DEFAULT NULL COMMENT '审核状态更新时间',
  `shop_no` varchar(10) NOT NULL DEFAULT '' COMMENT '商户号',
  `total_quota` int(10) NOT NULL DEFAULT '0' COMMENT '总额度',
  `daily_available_quota` int(10) NOT NULL DEFAULT '0' COMMENT '当日可用额度',
  `available_quota` int(10) NOT NULL DEFAULT '0' COMMENT '商户可用额度',
  `single_limit_quota` int(10) NOT NULL DEFAULT '0' COMMENT '单笔限额',
  `daily_limit_quota` int(10) NOT NULL DEFAULT '0' COMMENT '每日限额',
  `option` varchar(200) NOT NULL DEFAULT '' COMMENT '风控意见',
  `logo` varchar(100) NOT NULL DEFAULT '' COMMENT '商户logo',
  `shop_pic` varchar(500) NOT NULL DEFAULT '' COMMENT '商户店铺图片',
  `intro` varchar(200) NOT NULL DEFAULT '' COMMENT '商户介绍',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `registered_at` varchar(8) NOT NULL DEFAULT '' COMMENT '注册时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商户表';

# 添加借款表字段 已还款期数
alter table `loan` add `repayed_count` int(10) NOT NULL DEFAULT '0' COMMENT '已还款期数' after `repayed_amount`;

# 添加分期计划表字段  提前还款手续费
alter table `budget_plan` add `prepayment_fee` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '提前还款手续费' after `interest_fee`;

# 添加支付记录表字段 分期计划ID
alter table `pay_log` add `plan_id` varchar(80) NOT NULL DEFAULT '' COMMENT '订单包含的分期计划ID，多个计划使用,隔开' after `loan_id`;

# 变更loan表 字段类型
alter table `loan` modify `quota` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '申请额度';

# 变更商户表 字段类型
alter table `shop` modify `daily_available_quota`  decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '当日可用额度';
alter table `shop` modify `available_quota`  decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '商户可用额度';

# 变更用户表 字段类型
alter table `user` modify `fronzen_quota` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '冻结额度';
alter table `user` modify `available_quota` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '可用额度'; 

#更改商户表字段名 风控意见
ALTER TABLE `shop` CHANGE `option` `opinion` varchar(200) NOT NULL DEFAULT '' COMMENT '风控意见';

# 添加分期计划表字段 还款方式
ALTER TABLE `budget_plan` ADD `repayed_type` TINYINT(2) NOT NULL DEFAULT 0 COMMENT '还款方式，0-未还款 1-正常还款 2-提前还款' AFTER `state`;

CREATE TABLE `shop_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '' COMMENT '商家管理员名称',
  `password` varchar(64) NOT NULL DEFAULT '' COMMENT '密码',
  `shop_id` int(10) NOT NULL DEFAULT '0' COMMENT '商户id',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `login_ip` char(15) NOT NULL DEFAULT '' COMMENT '登入IP',
  `login_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '登入时间',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商家管理员';

# 更改shop表`registered_at字段注释
ALTER TABLE `shop` MODIFY `registered_at` varchar(8) NOT NULL DEFAULT '' COMMENT '公司注册时间' after `registered_capital`;

# 商户入驻
CREATE TABLE `shop_settled`(
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `shop_name` varchar(30) NOT NULL DEFAULT '' COMMENT '商户名称',
  `contacts_name` varchar(10) NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `contacts_mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '联系人手机号',
  `contacts_addr` varchar(50) NOT NULL DEFAULT '' COMMENT '联系地址',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY(`id`)
)ENGINE=INNODB DEFAULT CHARSET=utf8;

# 商户商品
CREATE TABLE `shop_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) NOT NULL DEFAULT '0' COMMENT '商户id',
  `category_id` int(10) NOT NULL DEFAULT '0' COMMENT '分类id',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '商品名称',
  `no` varchar(20) NOT NULL DEFAULT '' COMMENT '商品货号',
  `pic` varchar(500) NOT NULL DEFAULT '' COMMENT '图片',
  `sort` int(4) NOT NULL DEFAULT '0' COMMENT '排序',
  `intro` text NOT NULL COMMENT '产品介绍',
  `spec` text NOT NULL COMMENT '规格参数',
  `service` text NOT NULL COMMENT '售后',
  `on_sale` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:下架 1：上架',
  `sale` int(4) NOT NULL DEFAULT '0' COMMENT '销量',
  `total_stock` int(6) NOT NULL DEFAULT '0' COMMENT '总库存',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0：待审核 1：审核通过 2：审核不通过',
  `auditor_id` int(10) NOT NULL DEFAULT '0' COMMENT '审核人员ID',
  `audited_at` timestamp NULL DEFAULT NULL COMMENT '审核时间',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商户产品表';

# 商户商品规格价格表
CREATE TABLE `shop_pro_spec` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) NOT NULL DEFAULT '0' COMMENT '商户id',
  `spec` varchar(50) NOT NULL DEFAULT '' COMMENT '规格',
  `stock` int(6) NOT NULL DEFAULT '0' COMMENT '库存',
  `price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商户商品规格价格表';

# 商户产品表添加审核意见字段
alter TABLE `shop_product` add `opinion` varchar(200) NOT NULL DEFAULT '' COMMENT '审核意见' AFTER `state`;
# 修改商户商品规格价格字段product_id的备注
alter TABLE `shop_pro_spec` MODIFY `product_id` int(10) NOT NULL DEFAULT '0' COMMENT '商品id';


# 新建表 消费分期订单明细表
CREATE TABLE `order_detail`(
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` int(10) NOT NULL DEFAULT '0' COMMENT '借款ID',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '合计 = 单价 * 数量',
  `shop_product_id` int(10) NOT NULL DEFAULT '0' COMMENT '商家产品ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '商品名称',
  `spec` varchar(50) NOT NULL DEFAULT '' COMMENT '规格',
  `quantity` int(6) NOT NULL DEFAULT '0' COMMENT '数量',
  `price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '单价',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
   PRIMARY KEY (`id`)
)ENGINE = INNODB DEFAULT CHARSET=UTF8 COMMENT '消费分期订单明细表';
# loan表 添加字段 shop_id
ALTER TABLE `loan` add `shop_id` int(10) NOT NULL DEFAULT '0' COMMENT '商家ID' AFTER `user_id`;

# shop表修改
ALTER TABLE `shop` modify `total_quota` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '总额度';
ALTER TABLE `shop` modify `available_quota` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商户可用额度';
ALTER TABLE `shop` modify `single_limit_quota` decimal(10,2) NOT NULL DEFAULT '0.00' NOT NULL DEFAULT '0' COMMENT '单笔限额';
ALTER TABLE `shop` modify `daily_limit_quota` decimal(10,2) NOT NULL DEFAULT '0.00' NOT NULL DEFAULT '0' COMMENT '每日限额';
ALTER table `shop` add `init_total_quota` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '初始总额度' after total_quota;
ALTER table `shop` ADD `period` varchar(30) NOT NULL DEFAULT '' COMMENT '商户期数' AFTER `category`;

CREATE TABLE `shop_quota_apply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `shop_id` int(10) NOT NULL DEFAULT '0' COMMENT '商户ID',
  `admin_id` int(10) NOT NULL DEFAULT '0' COMMENT '审核员ID',
  `apply_total` int(10) NOT NULL DEFAULT '0' COMMENT '申请总额额度',
  `apply_single_limit` int(10) NOT NULL DEFAULT '0' COMMENT '申请单笔限额额度',
  `apply_daily_limit` int(10) NOT NULL DEFAULT '0' COMMENT '申请每日限额额度',
  `allow_total` int(10) NOT NULL DEFAULT '0' COMMENT '通过总额度',
  `allow_single_limit` int(10) NOT NULL DEFAULT '0' COMMENT '通过单笔限额额度',
  `allow_daily_limit` int(10) NOT NULL DEFAULT '0' COMMENT '通过每日限额额度',
  `available_quota` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商户可用额度',
  `total_quota` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '总额度',
  `single_limit_quota` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '单笔限额',
  `daily_limit_quota` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '每日限额',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '申请状态：0-待审核 1-审核',
  `state_total` tinyint(1) NOT NULL DEFAULT '0' COMMENT '申请总额度通过状态：0-待审核 1-审核通过 2-审核失败',
  `state_single_limit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '申请单笔限额通过状态：0-待审核 1-审核通过 2-审核失败',
  `state_daily_limit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '申请单日限额通过状态：0-待审核 1-审核通过 2-审核失败',
  `apply_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '额度类型：0-后台添加',
  `memo` varchar(300) NOT NULL DEFAULT '' COMMENT '备注',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间/申请时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_shop_id` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商户额度申请表';

# 更改用户申请额度记录表 可用额度、总额度的字段类型
ALTER TABLE `quota_apply` modify `available_quota` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '可用额度';
ALTER TABLE `quota_apply` modify `total_quota` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '总额度';

# 更改user_limu表的 platform_type 字段备注
ALTER TABLE `user_limu` MODIFY `platform_type` int(4) NOT NULL DEFAULT '0' COMMENT '平台类型：1-京东 2-淘宝 3-学信网 4-信用卡账单 5-网银流水 6-公积金 7-社保 8-央行征信';

# 添加亲签照字段
ALTER TABLE `user_basic` ADD `sign_pic` varchar(100) NOT NULL DEFAULT '' COMMENT '亲签照' AFTER `is_relation_auth`;

# user表添加用户认证状态字段
ALTER TABLE `user` ADD `is_credit_auth` tinyint(4) NOT NULL DEFAULT '0' COMMENT '央行征信认证 0:未填写/未认证 1：已认证' AFTER `is_picture_auth`;
ALTER TABLE `user` ADD `is_housefund_auth` tinyint(4) NOT NULL DEFAULT '0' COMMENT '公积金认证 0:未填写/未认证 1：已认证' AFTER `is_credit_auth`;
ALTER TABLE `user` ADD `is_socialsecurity_auth` tinyint(4) NOT NULL DEFAULT '0' COMMENT '社保认证 0:未填写/未认证 1：已认证' AFTER `is_housefund_auth`;

# 创建商户订单操作记录表
CREATE TABLE `shop_order_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `loan_id` int(10) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单状态：0-待确认 1-确认订单通过 2-确认订单失败 3-已收货 4-未收货',
  `confirm_opinion` varchar(200) NOT NULL DEFAULT '' COMMENT '确认订单意见',
  `receiving_opinion` varchar(200) NOT NULL DEFAULT '' COMMENT '确认收货意见',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间/申请时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商户订单操作记录表';

#添加商家确认订单时间字段
ALTER TABLE `loan` add `confirmed_at` timestamp NULL DEFAULT NULL COMMENT '商家确认时间' after `review_at`;

# loan表添加实际还款时间字段
ALTER TABLE `loan` add `repayment_at` timestamp NULL DEFAULT NULL COMMENT '实际还款时间' after lending_at;

CREATE TABLE `anti_fraud` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `content` varchar(2000) NOT NULL DEFAULT '' COMMENT '反欺诈内容',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unq_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

# overdue_log表 添加字段 plan_id
alter table `overdue_log` add `plan_id`int(10) NOT NULL DEFAULT '0'   COMMENT '分期计划ID' after `loan_id`;
# loan表 添加字段 tel_opinion、preliminary_result、tel_result
alter table `loan` add `tel_opinion` varchar(200) NOT NULL DEFAULT '' COMMENT '电审意见' after `preliminary_opinion`;
ALTER TABLE `loan` add `preliminary_result` SMALLINT(2) NOT NULL DEFAULT '0' COMMENT '初审结果 1-审核通过 2-审核不通过' after `preliminary_opinion`;
ALTER TABLE `loan` add `tel_result` SMALLINT(2) NOT NULL DEFAULT '0' COMMENT '电审结果 1-审核通过 2-审核不通过' after `tel_opinion`;

# 逾期催收表添加 分期计划id字段
alter TABLE `urge` add `budget_plan_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分期计划id' AFTER loan_id;
# 短信发送记录表 借款id字段`loan_id`改为分期计划id`budget_plan_id`
ALTER TABLE `mobile_log` CHANGE `loan_id` `budget_plan_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '借款分期计划id';

# order_detail 添加字段 spec_id
ALTER TABLE `order_detail` add `spec_id` int(10) NOT NULL DEFAULT '0' COMMENT '产品规格ID' AFTER `loan_id`;

# mobile_log表 修改字段 type 注释
ALTER TABLE `mobile_log` MODIFY `type` varchar(13) NOT NULL DEFAULT '' COMMENT '类型 auth_code:短信验证码 loan:放款通知 repayment:还款通知 overdue:逾期通知 repay_succ:还款成功 withdrawal:提现成功 overdue_mass:逾期群发短信 shop_confirm:商家确认提醒';

# shop 表修改 分类字段
ALTER TABLE `shop` DROP COLUMN `category`;
ALTER TABLE `shop` ADD `shop_category` varchar(30) NOT NULL DEFAULT '' COMMENT '商户可分期类别' AFTER `city_id`;
ALTER TABLE `shop` ADD `product_category` varchar(40) NOT NULL DEFAULT '' COMMENT '商品可分期类别' AFTER `shop_category`;

# pay_log 表添加字段 计划详情
alter table `pay_log` add `plan_detail` text COMMENT '计划详情' after `settle_date`;

# 意见反馈表
CREATE TABLE `feedback` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `type` varchar(10) NOT NULL DEFAULT '' COMMENT '类型',
  `content` varchar(1000) NOT NULL DEFAULT '' COMMENT ' 内容',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态 1:未处理 2:已处理',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='意见反馈';

# pay_log表添加字段 bank_code、card_no
alter table `pay_log` add `bank_code` VARCHAR(19) null COMMENT'银行编号' after `settle_date`;
alter table `pay_log` add `card_no` VARCHAR(8) null COMMENT'银行卡号' after `bank_code`;

# pay_log表 修改字段
alter table `pay_log` modify `bank_code` VARCHAR(19) DEFAULT '' COMMENT'银行编号' after `settle_date`;
alter table `pay_log` modify `card_no` VARCHAR(8) DEFAULT '' COMMENT'银行卡号' after `bank_code`;
alter table `pay_log` add `no_agree` VARCHAR(16) DEFAULT '' COMMENT'银通签约的协议编号' after `card_no`;

# user表 修改字段注释
ALTER TABLE `user` MODIFY `is_phone_auth` tinyint(4) NOT NULL DEFAULT '0' COMMENT '手机认证 0:未填写/未认证 1:已认证 2:已提交';

# user_basic 表删除亲签照字段
ALTER TABLE `user_basic` DROP `sign_pic`;

# 亲签照表
CREATE TABLE `visa` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `shop_id` int(10) NOT NULL DEFAULT '0' COMMENT '商家ID',
  `sign_pic` varchar(100) NOT NULL DEFAULT '' COMMENT '亲签照',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='亲签照';

# user 表删除亲签照认证字段
ALTER TABLE `user` DROP `is_picture_auth`;

# 更新亲签照表时间字段默认值
ALTER TABLE `visa` modify `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间';
ALTER TABLE `visa` modify `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间';

# 更新user表 可用额度默认值
ALTER TABLE `user` MODIFY `available_quota` decimal(8,2) NOT NULL DEFAULT '10000' COMMENT '可用额度';

# 新增数据库索引
# mobile_log
ALTER TABLE mobile_log DROP INDEX idx_moblie_loan_id ;
ALTER TABLE mobile_log ADD INDEX `idx_moblie_budget_plan_id` (`mobile`,`budget_plan_id`);
# shop
ALTER TABLE shop ADD INDEX `idx_shop_no` (`shop_no`);
#shop_order_log
ALTER TABLE shop_order_log ADD INDEX `idx_loan_id` (`loan_id`);
# shop_pro_spec
ALTER TABLE shop_pro_spec ADD INDEX `idx_product_id` (`product_id`);
# shop_product
ALTER TABLE shop_product ADD INDEX `idx_shop_id` (`shop_id`);
# urge
ALTER TABLE urge ADD INDEX `idx_budget_plan_id` (`budget_plan_id`);
# visa
ALTER TABLE visa ADD INDEX `idx_user_id_shop_id` (`user_id`, `shop_id`);
# order_detail
ALTER TABLE order_detail ADD INDEX `idx_shop_product_id` (`shop_product_id`);
ALTER TABLE order_detail ADD INDEX `idx_loan_id` (`loan_id`);
# budget_plan
ALTER TABLE budget_plan ADD INDEX `idx_user_id` (`user_id`);
ALTER TABLE budget_plan ADD INDEX `idx_loan_id` (`loan_id`);
# loan
ALTER TABLE loan ADD INDEX `idx_user_id` (`user_id`);
ALTER TABLE loan ADD INDEX `idx_shop_id` (`shop_id`);
# overdue_log
ALTER TABLE overdue_log ADD INDEX `idx_plan_id` (`plan_id`);
# shop_product
ALTER TABLE shop_product ADD INDEX `idx_category_id` (`category_id`);