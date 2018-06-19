SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `admin`
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='管理员';

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('1', 'admin', '$2y$13$HCdQmp4o7/zu2Bw8ls0/DuTsXA6eeAOq4sF1YXGOUwX0EfzpkRn/e', '颜巧文', '1', '127.0.0.1', '2018-03-14 13:33:32', '2017-07-13 21:30:46', '2017-07-13 21:47:46');

-- ----------------------------
-- Table structure for `auth_assignment`
-- ----------------------------
DROP TABLE IF EXISTS `auth_assignment`;
CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_assignment
-- ----------------------------
INSERT INTO `auth_assignment` VALUES ('超级管理员', '1', '1500017917');

-- ----------------------------
-- Table structure for `auth_item`
-- ----------------------------
DROP TABLE IF EXISTS `auth_item`;
CREATE TABLE `auth_item` (
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_item
-- ----------------------------
INSERT INTO `auth_item` VALUES ('/admin/add', '2', '添加管理员', null, null, '1500017917', '1500017917');
INSERT INTO `auth_item` VALUES ('/admin/basic', '2', '基础信息', null, null, '1500017917', '1500017917');
INSERT INTO `auth_item` VALUES ('/admin/detail', '2', '管理员详情', null, null, '1500017917', '1500017917');
INSERT INTO `auth_item` VALUES ('/admin/index', '2', '管理员列表', null, null, '1500017917', '1501050125');
INSERT INTO `auth_item` VALUES ('/admin/set-leave', '2', '设置离职', null, null, '1500892022', '1500892097');
INSERT INTO `auth_item` VALUES ('/admin/set-password', '2', '修改密码', null, null, '1521005598', '1521005598');
INSERT INTO `auth_item` VALUES ('/admin/update', '2', '编辑管理员', null, null, '1500017917', '1500017917');
INSERT INTO `auth_item` VALUES ('/auth/add', '2', '添加权限', null, null, '1500017917', '1500881239');
INSERT INTO `auth_item` VALUES ('/auth/assign', '2', '分配权限', null, null, '1500017917', '1500881239');
INSERT INTO `auth_item` VALUES ('/auth/delete', '2', '删除权限', null, null, '1501138669', '1501138669');
INSERT INTO `auth_item` VALUES ('/auth/detail', '2', '权限详情', null, null, '1500892062', '1500892062');
INSERT INTO `auth_item` VALUES ('/auth/index', '2', '权限管理', null, null, '1500017917', '1500881239');
INSERT INTO `auth_item` VALUES ('/auth/role-auths', '2', '角色权限', null, null, '1500017917', '1500881239');
INSERT INTO `auth_item` VALUES ('/auth/update', '2', '编辑权限', null, null, '1500017917', '1500881239');
INSERT INTO `auth_item` VALUES ('/menu/mine', '2', '我的菜单', null, null, '1500017917', '1500881247');
INSERT INTO `auth_item` VALUES ('/role/add', '2', '添加角色', null, null, '1500017917', '1500881247');
INSERT INTO `auth_item` VALUES ('/role/detail', '2', '角色详情', null, null, '1500892982', '1500892982');
INSERT INTO `auth_item` VALUES ('/role/index', '2', '角色管理', null, null, '1500017917', '1500881247');
INSERT INTO `auth_item` VALUES ('/role/update', '2', '编辑角色', null, null, '1500017917', '1500881247');
INSERT INTO `auth_item` VALUES ('/site/logout', '2', '退出后台', null, null, '1521008643', '1521008643');
INSERT INTO `auth_item` VALUES ('backend_menus_permissions_admins', '2', '账号管理', null, null, '1495703476', '1495703476');
INSERT INTO `auth_item` VALUES ('backend_menus_permissions_auths', '2', '权限管理', null, null, '1495703476', '1501049835');
INSERT INTO `auth_item` VALUES ('backend_menus_permissions_roles', '2', '角色管理', null, null, '1495703476', '1495703476');
INSERT INTO `auth_item` VALUES ('超级管理员', '1', '最高权限（所有功能）', null, null, '1500017917', '1500893019');

-- ----------------------------
-- Table structure for `auth_item_child`
-- ----------------------------
DROP TABLE IF EXISTS `auth_item_child`;
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_item_child
-- ----------------------------
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/add');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/basic');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/detail');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/index');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/set-leave');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/set-password');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/update');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/auth/add');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/auth/assign');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/auth/delete');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/auth/detail');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/auth/index');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/auth/role-auths');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/auth/update');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/menu/mine');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/role/add');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/role/detail');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/role/index');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/role/update');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/site/logout');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'backend_menus_education_auth');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'backend_menus_permissions_admins');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'backend_menus_permissions_auths');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'backend_menus_permissions_roles');

-- ----------------------------
-- Table structure for `auth_rule`
-- ----------------------------
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_rule
-- ----------------------------

-- ----------------------------
-- Table structure for `partner`
-- ----------------------------
DROP TABLE IF EXISTS `partner`;
CREATE TABLE `partner` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL DEFAULT '' COMMENT '名称',
  `description` varchar(50) NOT NULL DEFAULT '' COMMENT '描述',
  `link` varchar(100) NOT NULL DEFAULT '' COMMENT '链接地址',
  `image` varchar(100) NOT NULL DEFAULT '' COMMENT '图片地址',
  `sort` int(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示1:显示 2:隐藏',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='合作伙伴';

-- ----------------------------
-- Table structure for `navigation`
-- ----------------------------
DROP TABLE IF EXISTS `navigation`;
CREATE TABLE `navigation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL DEFAULT '' COMMENT '导航名称',
  `description` varchar(50) NOT NULL DEFAULT '' COMMENT '分类描述',
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '上级导航id，默认0为顶级',
  `link` varchar(50) NOT NULL DEFAULT '' COMMENT '链接地址',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类型 1:文本列表 2:图片列表 3:页面 4:内容',
  `sort` int(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示：1：显示, 2:隐藏',
  `is_open` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否打开新页面：1:是, 2:否',
  `admin_id` int(10) NOT NULL DEFAULT '0' COMMENT '管理员id',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='导航';

-- ----------------------------
-- Records of navigation
-- ----------------------------

-- ----------------------------
-- Table structure for `example`
-- ----------------------------
DROP TABLE IF EXISTS `example`;
CREATE TABLE `example` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL DEFAULT '' COMMENT '案例名称',
  `description` varchar(50) NOT NULL DEFAULT '' COMMENT '描述',
  `link` varchar(100) NOT NULL DEFAULT '' COMMENT '链接地址',
  `image` varchar(100) NOT NULL DEFAULT '' COMMENT '图片地址',
  `sort` int(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `nav_id` int(10) NOT NULL DEFAULT '0' COMMENT '导航id',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示1:显示 2:隐藏',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='案例';

-- ----------------------------
-- Records of example
-- ----------------------------

-- ----------------------------
-- Table structure for `banner`
-- ----------------------------
DROP TABLE IF EXISTS `banner`;
CREATE TABLE `banner` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL DEFAULT '' COMMENT 'banner名称',
  `description` varchar(50) NOT NULL DEFAULT '' COMMENT '描述',
  `link` varchar(100) NOT NULL DEFAULT '' COMMENT '链接地址',
  `image` varchar(100) NOT NULL DEFAULT '' COMMENT 'banner图片地址',
  `sort` int(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示1:显示 2:隐藏',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='banner';

-- ----------------------------
-- Records of banner
-- ----------------------------

-- ----------------------------
-- Table structure for `article`
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `description` varchar(50) NOT NULL DEFAULT '' COMMENT '描述',
  `nav_id` int(10) NOT NULL DEFAULT '0' COMMENT '导航id',
  `image` varchar(100) NOT NULL DEFAULT '' COMMENT '图片地址',
  `sort` int(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示1:显示 2:隐藏',
  `notice` tinyint(1) NOT NULL DEFAULT '2' COMMENT '是否显示在公告1:显示 2:不显示',
  `content` text NOT NULL COMMENT ' 内容',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章表';

-- ----------------------------
-- Records of article
-- ----------------------------

INSERT INTO `auth_item` VALUES ('/article/add', '2', '添加文章', null, null, '1521511630', '1521511630');
INSERT INTO `auth_item` VALUES ('/article/del', '2', '删除文章', null, null, '1521511668', '1521511668');
INSERT INTO `auth_item` VALUES ('/article/get-article', '2', '获取一条文章', null, null, '1521517579', '1521517579');
INSERT INTO `auth_item` VALUES ('/article/index', '2', '文章列表', null, null, '1521511582', '1521511582');
INSERT INTO `auth_item` VALUES ('/article/need', '2', '添加文章时下发分类', null, null, '1521517538', '1521517538');
INSERT INTO `auth_item` VALUES ('/article/update', '2', '编辑文章', null, null, '1521511654', '1521511654');
INSERT INTO `auth_item` VALUES ('/banner/add', '2', '添加banner', null, null, '1521432063', '1521432063');
INSERT INTO `auth_item` VALUES ('/banner/del', '2', '删除banner', null, null, '1521432114', '1521432114');
INSERT INTO `auth_item` VALUES ('/banner/index', '2', 'banner列表', null, null, '1521432035', '1521432035');
INSERT INTO `auth_item` VALUES ('/banner/update', '2', '编辑banner', null, null, '1521432093', '1521432093');
INSERT INTO `auth_item` VALUES ('/example/add', '2', '添加案例', null, null, '1521444202', '1521444202');
INSERT INTO `auth_item` VALUES ('/example/del', '2', '删除案例', null, null, '1521444238', '1521444238');
INSERT INTO `auth_item` VALUES ('/example/index', '2', '案例列表', null, null, '1521444184', '1521444184');
INSERT INTO `auth_item` VALUES ('/example/update', '2', '编辑案例', null, null, '1521444226', '1521444226');
INSERT INTO `auth_item` VALUES ('/nav/add', '2', '添加导航', null, null, '1521105182', '1521105182');
INSERT INTO `auth_item` VALUES ('/nav/del', '2', '删除导航', null, null, '1521171324', '1521171324');
INSERT INTO `auth_item` VALUES ('/nav/get-nav', '2', '根据上级id获取导航', null, null, '1521446489', '1521446489');
INSERT INTO `auth_item` VALUES ('/nav/index', '2', '导航列表', null, null, '1521084821', '1521084821');
INSERT INTO `auth_item` VALUES ('/nav/update', '2', '修改导航', null, null, '1521162530', '1521162530');
INSERT INTO `auth_item` VALUES ('/partner/add', '2', '添加合作伙伴', null, null, '1521424930', '1521424930');
INSERT INTO `auth_item` VALUES ('/partner/del', '2', '删除合作伙伴', null, null, '1521429959', '1521429959');
INSERT INTO `auth_item` VALUES ('/partner/index', '2', '合作伙伴列表', null, null, '1521193930', '1521193930');
INSERT INTO `auth_item` VALUES ('/partner/update', '2', '编辑合作伙伴', null, null, '1521429307', '1521429307');
INSERT INTO `auth_item` VALUES ('/system/set-params', '2', '案例中心设置参数', null, null, '1521448349', '1521448349');
INSERT INTO `auth_item` VALUES ('backend_menus_article', '2', '文章管理', null, null, '1521510110', '1521510110');
INSERT INTO `auth_item` VALUES ('backend_menus_banner', '2', 'banner管理', null, null, '1521194916', '1521194916');
INSERT INTO `auth_item` VALUES ('backend_menus_example', '2', '案例中心', null, null, '1521443434', '1521443434');
INSERT INTO `auth_item` VALUES ('backend_menus_nav', '2', '导航列表', null, null, '1521084148', '1521084148');
INSERT INTO `auth_item` VALUES ('backend_menus_partner', '2', '合作伙伴列表', null, null, '1521193766', '1521193766');
INSERT INTO `auth_item` VALUES ('/site/upload', '2', '上传图片', null, null, '1521425742', '1521425742');

# 文章添加作者字段
ALTER TABLE `article`
add `author` varchar(10) NOT NULL DEFAULT '' COMMENT '作者'
after `title`;