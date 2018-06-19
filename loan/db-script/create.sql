CREATE TABLE `admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '' COMMENT '管理员名称',
  `password` varchar(64) NOT NULL DEFAULT '' COMMENT '密码',
  `real_name` varchar(10) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `is_super` tinyint(1) NOT NULL DEFAULT '0' COMMENT '超级管理员',
  `role_id` int(10) NOT NULL DEFAULT '0' COMMENT '角色',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `login_ip` char(15) NOT NULL DEFAULT '' COMMENT '登入IP',
  `login_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '登入时间',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员';

# user表 修改字段注释
ALTER TABLE `user` MODIFY `is_phone_auth` tinyint(4) NOT NULL DEFAULT '0' COMMENT '手机认证 0:未填写/未认证 1:已认证 2:已提交';