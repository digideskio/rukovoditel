DROP TABLE IF EXISTS app_access_groups;
DROP TABLE IF EXISTS app_attachments;
DROP TABLE IF EXISTS app_comments;
DROP TABLE IF EXISTS app_comments_access;
DROP TABLE IF EXISTS app_comments_history;
DROP TABLE IF EXISTS app_configuration;
DROP TABLE IF EXISTS app_entities;
DROP TABLE IF EXISTS app_entities_access;
DROP TABLE IF EXISTS app_entities_configuration;
DROP TABLE IF EXISTS app_entity_1;
DROP TABLE IF EXISTS app_entity_21;
DROP TABLE IF EXISTS app_entity_22;
DROP TABLE IF EXISTS app_entity_23;
DROP TABLE IF EXISTS app_entity_24;
DROP TABLE IF EXISTS app_ext_calendar;
DROP TABLE IF EXISTS app_ext_calendar_access;
DROP TABLE IF EXISTS app_ext_calendar_events;
DROP TABLE IF EXISTS app_ext_ganttchart;
DROP TABLE IF EXISTS app_ext_ganttchart_access;
DROP TABLE IF EXISTS app_ext_ganttchart_depends;
DROP TABLE IF EXISTS app_ext_graphicreport;
DROP TABLE IF EXISTS app_fields;
DROP TABLE IF EXISTS app_fields_access;
DROP TABLE IF EXISTS app_fields_choices;
DROP TABLE IF EXISTS app_forms_tabs;
DROP TABLE IF EXISTS app_related_items;
DROP TABLE IF EXISTS app_reports;
DROP TABLE IF EXISTS app_reports_filters;
DROP TABLE IF EXISTS app_sessions;
DROP TABLE IF EXISTS app_users_configuration;


CREATE TABLE `app_access_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  `is_ldap_default` tinyint(1) DEFAULT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

INSERT INTO app_access_groups VALUES
('5','デベロッパー','0','0','1'),
('4','マネージャー','1','0','2'),
('6','クライアント','0','0','0');

CREATE TABLE `app_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_token` varchar(64) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `date_added` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `app_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `description` text,
  `attachments` text NOT NULL,
  `date_added` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO app_comments VALUES
('3','21','1','21','全然緊急じゃないよ','','1441882060');

CREATE TABLE `app_comments_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `access_groups_id` int(11) NOT NULL,
  `access_schema` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_access_groups_id` (`access_groups_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

INSERT INTO app_comments_access VALUES
('4','21','6','view,create'),
('5','21','5','view,create'),
('6','21','4','view,create,update,delete'),
('7','22','5','view,create'),
('8','22','4','view,create,update,delete'),
('9','23','6','view,create'),
('10','23','4','view,create,update,delete'),
('11','24','5','view,create'),
('12','24','4','view,create,update,delete');

CREATE TABLE `app_comments_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comments_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `fields_value` text,
  PRIMARY KEY (`id`),
  KEY `idx_comments_id` (`comments_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO app_comments_history VALUES
('4','3','156','35');

CREATE TABLE `app_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `configuration_name` varchar(255) NOT NULL DEFAULT '',
  `configuration_value` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;

INSERT INTO app_configuration VALUES
('11','CFG_APP_LOGO','Adobe_logo_Horizontal_red_white_gif.gif'),
('10','CFG_APP_SHORT_NAME','Rukov'),
('9','CFG_APP_NAME','Rukovoditel'),
('12','CFG_EMAIL_USE_NOTIFICATION','1'),
('13','CFG_EMAIL_SUBJECT_LABEL',NULL),
('14','CFG_EMAIL_AMOUNT_PREVIOUS_COMMENTS','2'),
('15','CFG_EMAIL_COPY_SENDER','0'),
('16','CFG_EMAIL_SEND_FROM_SINGLE','0'),
('17','CFG_EMAIL_ADDRESS_FROM',NULL),
('18','CFG_EMAIL_NAME_FROM',NULL),
('19','CFG_EMAIL_USE_SMTP','0'),
('20','CFG_EMAIL_SMTP_SERVER',NULL),
('21','CFG_EMAIL_SMTP_PORT',NULL),
('22','CFG_EMAIL_SMTP_ENCRYPTION',NULL),
('23','CFG_EMAIL_SMTP_LOGIN',NULL),
('24','CFG_EMAIL_SMTP_PASSWORD',NULL),
('25','CFG_LDAP_USE','0'),
('26','CFG_LDAP_SERVER_NAME',NULL),
('27','CFG_LDAP_SERVER_PORT',NULL),
('28','CFG_LDAP_BASE_DN',NULL),
('29','CFG_LDAP_UID',NULL),
('30','CFG_LDAP_USER',NULL),
('31','CFG_LDAP_EMAIL_ATTRIBUTE',NULL),
('32','CFG_LDAP_USER_DN',NULL),
('33','CFG_LDAP_PASSWORD',NULL),
('34','CFG_LOGIN_PAGE_HEADING',NULL),
('35','CFG_LOGIN_PAGE_CONTENT',NULL),
('36','CFG_APP_TIMEZONE','Asia/Tokyo'),
('37','CFG_APP_DATE_FORMAT','Y/m/d'),
('38','CFG_APP_DATETIME_FORMAT','Y/m/d H:i'),
('39','CFG_APP_ROWS_PER_PAGE','10'),
('40','CFG_REGISTRATION_EMAIL_SUBJECT',NULL),
('41','CFG_REGISTRATION_EMAIL_BODY',NULL),
('42','CFG_PASSWORD_MIN_LENGTH','3'),
('43','CFG_APP_LANGUAGE','japanese.php'),
('44','CFG_APP_SKIN',NULL),
('45','CFG_PUBLIC_USER_PROFILE_FIELDS',NULL),
('46','CFG_APP_FIRST_DAY_OF_WEEK','1'),
('47','CFG_PLUGIN_EXT_INSTALLED','1'),
('48','CFG_PLUGIN_EXT_LICENSE_KEY','13176135421207811834131761268813542140301415270766832683268326832');

CREATE TABLE `app_entities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `display_in_menu` tinyint(1) DEFAULT '0',
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

INSERT INTO app_entities VALUES
('1','0','ユーザー','0','10'),
('22','21','タスク','0','1'),
('23','21','チケット','0','2'),
('24','21','ディスカッション','0','3'),
('21','0','プロジェクト','0','1');

CREATE TABLE `app_entities_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `access_groups_id` int(11) NOT NULL,
  `access_schema` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_access_groups_id` (`access_groups_id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

INSERT INTO app_entities_access VALUES
('28','21','6','view_assigned'),
('29','21','5','view_assigned,reports'),
('30','21','4','view,create,update,delete,reports'),
('31','22','6',''),
('32','22','5','view,create,update,reports'),
('33','22','4','view,create,update,delete,reports'),
('34','23','6','view_assigned,create,update,reports'),
('35','23','5',''),
('36','23','4','view,create,update,delete,reports'),
('37','24','6',''),
('38','24','5','view_assigned,create,update,delete,reports'),
('39','24','4','view,create,update,delete,reports');

CREATE TABLE `app_entities_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `configuration_name` varchar(255) NOT NULL DEFAULT '',
  `configuration_value` text,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=MyISAM AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;

INSERT INTO app_entities_configuration VALUES
('30','21','use_comments','1'),
('29','21','email_subject_new_item','新プロジェクト: '),
('28','21','insert_button','プロジェクトを追加'),
('11','1','menu_title','ユーザー'),
('12','1','listing_heading','ユーザー'),
('13','1','window_heading','ユーザー情報'),
('14','1','insert_button','ユーザーを追加'),
('15','1','use_comments','0'),
('27','21','window_heading','プロジェクト情報'),
('25','21','menu_title','プロジェクト'),
('26','21','listing_heading','プロジェクト'),
('32','22','menu_title','タスク'),
('31','21','email_subject_new_comment','プロジェクトコメント: '),
('33','22','listing_heading','タスク'),
('34','22','window_heading','タスク情報'),
('35','22','insert_button','タスクを追加'),
('36','22','email_subject_new_item','新しいタスク'),
('37','22','use_comments','1'),
('38','22','email_subject_new_comment','新しいタスクコメント:'),
('39','23','menu_title','チケット'),
('40','23','listing_heading','チケット'),
('41','23','window_heading','チケット情報'),
('42','23','insert_button','チケット追加'),
('43','23','email_subject_new_item','新しいチケット: '),
('44','23','use_comments','1'),
('45','23','email_subject_new_comment','チケットに新しいコメントがありました'),
('46','24','menu_title','ディスカッション'),
('47','24','listing_heading','ディスカッション'),
('48','24','window_heading','ディスカッション情報'),
('49','24','insert_button','ディスカッションを追加'),
('50','24','email_subject_new_item','新ディスカッション:'),
('51','24','use_comments','1'),
('52','24','email_subject_new_comment','ディスカッションに新しいコメント: '),
('53','21','use_editor_in_comments','0'),
('54','22','use_editor_in_comments','0'),
('55','23','use_editor_in_comments','0'),
('56','24','use_editor_in_comments','0'),
('57','23','menu_icon',NULL),
('58','1','menu_icon','fa-users'),
('59','1','email_subject_new_item',NULL),
('60','1','use_editor_in_comments','0'),
('61','1','email_subject_new_comment',NULL),
('62','21','menu_icon',NULL),
('63','22','menu_icon',NULL),
('64','24','menu_icon',NULL);

CREATE TABLE `app_entity_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `parent_item_id` int(11) NOT NULL DEFAULT '0',
  `linked_id` int(11) NOT NULL DEFAULT '0',
  `date_added` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `password` varchar(255) NOT NULL,
  `field_5` text,
  `field_6` text,
  `field_7` text,
  `field_8` text,
  `field_9` text,
  `field_10` text,
  `field_12` text,
  `field_13` text,
  `field_14` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_parent_item_id` (`parent_item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

INSERT INTO app_entity_1 VALUES
('19','0','0','0','0',NULL,'0','$P$ES99JiKpVV7tbPctSEL2H0L8h3opT7.','1','0','Rukovoditel','Admin','toshimi+rukovoditel@adobe.com',NULL,'admin','japanese.php','blue'),
('20','0','0','0','1441880461','19','0','$P$El7rR6AR4oWkDOktTnDASH3ieqpHM4/','1','4','俊巳','畠中','toshimi@adobe.com',NULL,'toshimi','japanese.php','blue'),
('21','0','0','0','1441881756','19','0','$P$EQCi1sshX1eyWKmrDuv29UN2ChTnnm/','1','4','芳理','及川','koikawa@adobe.com',NULL,'kaori','japanese.php','blue');

CREATE TABLE `app_entity_21` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT '0',
  `parent_item_id` int(11) DEFAULT '0',
  `linked_id` int(11) DEFAULT '0',
  `date_added` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  `field_156` text NOT NULL,
  `field_157` text NOT NULL,
  `field_158` text NOT NULL,
  `field_159` text NOT NULL,
  `field_160` text NOT NULL,
  `field_161` text NOT NULL,
  `field_162` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_parent_item_id` (`parent_item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO app_entity_21 VALUES
('1','0','0','0','1441880858','20','0','35','37','あれれ','1441810800','<p>これは大変です。</p>\r\n','19,20',''),
('2','0','0','0','1441881830','21','0','35','37','大雨対策','1442329200','<p>すぐになんとかしなきゃいけんの。</p>\r\n','','');

CREATE TABLE `app_entity_22` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT '0',
  `parent_item_id` int(11) DEFAULT '0',
  `linked_id` int(11) DEFAULT '0',
  `date_added` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  `field_167` text NOT NULL,
  `field_168` text NOT NULL,
  `field_169` text NOT NULL,
  `field_170` text NOT NULL,
  `field_171` text NOT NULL,
  `field_172` text NOT NULL,
  `field_173` text NOT NULL,
  `field_174` text NOT NULL,
  `field_175` text NOT NULL,
  `field_176` text NOT NULL,
  `field_177` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_parent_item_id` (`parent_item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO app_entity_22 VALUES
('1','0','1','0','1441881933','21','0','42','ああああ','46','55','20','<p>これこれこれ</p>\r\n','7','','1441810800','1443106800','');

CREATE TABLE `app_entity_23` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT '0',
  `parent_item_id` int(11) DEFAULT '0',
  `linked_id` int(11) DEFAULT '0',
  `date_added` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  `field_182` text NOT NULL,
  `field_183` text NOT NULL,
  `field_184` text NOT NULL,
  `field_185` text NOT NULL,
  `field_186` text NOT NULL,
  `field_194` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_parent_item_id` (`parent_item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO app_entity_23 VALUES
('1','0','1','0','1441882005','21','0','56|','58','ばぐですぜ','<p>ばぐばぐ</p>\r\n','61','');

CREATE TABLE `app_entity_24` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT '0',
  `parent_item_id` int(11) DEFAULT '0',
  `linked_id` int(11) DEFAULT '0',
  `date_added` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  `field_191` text NOT NULL,
  `field_192` text NOT NULL,
  `field_193` text NOT NULL,
  `field_195` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_parent_item_id` (`parent_item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO app_entity_24 VALUES
('1','0','1','0','1441882032','21','0','これどうする？','<p>どうしましょ</p>\r\n','67','');

CREATE TABLE `app_ext_calendar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `start_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `app_ext_calendar_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calendar_id` int(11) DEFAULT NULL,
  `calendar_type` varchar(16) NOT NULL,
  `access_groups_id` int(11) NOT NULL,
  `access_schema` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_calendar_id` (`calendar_id`),
  KEY `idx_access_groups_id` (`access_groups_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `app_ext_calendar_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `start_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  `event_type` varchar(16) NOT NULL,
  `is_public` tinyint(1) DEFAULT NULL,
  `bg_color` varchar(16) DEFAULT NULL,
  `repeat_type` varchar(16) DEFAULT NULL,
  `repeat_interval` int(11) DEFAULT NULL,
  `repeat_days` varchar(16) DEFAULT NULL,
  `repeat_end` int(11) DEFAULT NULL,
  `repeat_limit` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `app_ext_ganttchart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `start_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  `weekends` varchar(16) DEFAULT NULL,
  `gantt_date_format` varchar(16) NOT NULL,
  `progress` int(11) DEFAULT NULL,
  `fields_in_listing` text,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `app_ext_ganttchart_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ganttchart_id` int(11) NOT NULL,
  `access_groups_id` int(11) NOT NULL,
  `access_schema` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ganttchart_id` (`ganttchart_id`),
  KEY `idx_access_groups_id` (`access_groups_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `app_ext_ganttchart_depends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ganttchart_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `depends_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_item_id` (`item_id`),
  KEY `idx_depends_id` (`depends_id`),
  KEY `idx_ganttchart_id` (`ganttchart_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `app_ext_graphicreport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `xaxis` int(11) NOT NULL,
  `yaxis` varchar(255) NOT NULL,
  `allowed_groups` int(11) DEFAULT NULL,
  `chart_type` varchar(16) NOT NULL,
  `period` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `app_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `forms_tabs_id` int(11) NOT NULL,
  `type` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `short_name` varchar(64) DEFAULT NULL,
  `is_heading` tinyint(1) DEFAULT '0',
  `tooltip` text,
  `is_required` tinyint(1) DEFAULT '0',
  `required_message` text,
  `configuration` text,
  `sort_order` int(11) DEFAULT '0',
  `listing_status` tinyint(4) NOT NULL DEFAULT '0',
  `listing_sort_order` int(11) NOT NULL DEFAULT '0',
  `comments_status` tinyint(1) NOT NULL DEFAULT '0',
  `comments_sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_form_tabs_id` (`forms_tabs_id`)
) ENGINE=MyISAM AUTO_INCREMENT=196 DEFAULT CHARSET=utf8;

INSERT INTO app_fields VALUES
('1','1','1','fieldtype_action','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','0','0','0'),
('2','1','1','fieldtype_id','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','1','0','0'),
('3','1','1','fieldtype_date_added','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'0','0','0','0'),
('4','1','1','fieldtype_created_by','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'0','0','0','0'),
('5','1','1','fieldtype_user_status','',NULL,NULL,NULL,NULL,NULL,NULL,'0','1','7','0','0'),
('6','1','1','fieldtype_user_accessgroups','',NULL,NULL,NULL,NULL,NULL,NULL,'2','1','2','0','0'),
('7','1','1','fieldtype_user_firstname','',NULL,NULL,NULL,NULL,NULL,'{\"allow_search\":\"1\"}','5','1','5','0','0'),
('8','1','1','fieldtype_user_lastname','',NULL,NULL,NULL,NULL,NULL,'{\"allow_search\":\"1\"}','4','1','4','0','0'),
('9','1','1','fieldtype_user_email','',NULL,NULL,NULL,NULL,NULL,'{\"allow_search\":\"1\"}','7','1','6','0','0'),
('10','1','1','fieldtype_user_photo','',NULL,NULL,NULL,NULL,NULL,NULL,'6','0','0','0','0'),
('12','1','1','fieldtype_user_username','',NULL,'1',NULL,NULL,NULL,'{\"allow_search\":\"1\"}','3','1','3','0','0'),
('178','23','28','fieldtype_action','',NULL,'0',NULL,'0',NULL,NULL,'0','1','0','0','0'),
('177','22','26','fieldtype_attachments','添付ファイル',NULL,'0',NULL,'0',NULL,NULL,'7','0','0','0','0'),
('176','22','27','fieldtype_input_date','終了日',NULL,'0',NULL,'0',NULL,'{\"background\":\"\"}','4','1','9','0','0'),
('171','22','26','fieldtype_users','アサイン先',NULL,'0',NULL,'0',NULL,'{\"display_as\":\"checkboxes\"}','5','1','6','0','0'),
('174','22','27','fieldtype_input_numeric_comments','作業時間',NULL,'0',NULL,'0',NULL,NULL,'2','1','8','1','2'),
('173','22','27','fieldtype_input_numeric','見積もり時間',NULL,'0',NULL,'0',NULL,NULL,'1','1','7','0','0'),
('172','22','26','fieldtype_textarea_wysiwyg','説明',NULL,'0',NULL,'0',NULL,'{\"allow_search\":\"1\"}','6','0','0','0','0'),
('14','1','1','fieldtype_user_skin','',NULL,'0',NULL,'0',NULL,NULL,'1','0','0','0','0'),
('13','1','1','fieldtype_user_language','',NULL,'0',NULL,'0',NULL,NULL,'8','0','0','0','0'),
('170','22','26','fieldtype_dropdown','優先度',NULL,'0',NULL,'1',NULL,'{\"width\":\"input-medium\",\"use_search\":\"0\"}','4','1','2','1','1'),
('169','22','26','fieldtype_dropdown','ステータス',NULL,'0',NULL,'1',NULL,'{\"width\":\"input-large\",\"use_search\":\"0\"}','3','1','5','1','0'),
('164','22','26','fieldtype_id','',NULL,'0',NULL,'0',NULL,NULL,'0','1','1','0','0'),
('165','22','26','fieldtype_date_added','',NULL,'0',NULL,'0',NULL,NULL,'0','1','10','0','0'),
('166','22','26','fieldtype_created_by','',NULL,'0',NULL,'0',NULL,NULL,'0','1','11','0','0'),
('167','22','26','fieldtype_dropdown','タイプ',NULL,'0',NULL,'1',NULL,'{\"width\":\"input-medium\",\"use_search\":\"0\"}','1','1','3','0','0'),
('168','22','26','fieldtype_input','名前',NULL,'1',NULL,'1',NULL,'{\"allow_search\":\"1\",\"width\":\"input-xlarge\"}','2','1','4','0','0'),
('184','23','28','fieldtype_input','タイトル',NULL,'1',NULL,'1',NULL,'{\"allow_search\":\"1\",\"width\":\"input-xlarge\"}','3','1','3','0','0'),
('180','23','28','fieldtype_date_added','',NULL,'0',NULL,'0',NULL,NULL,'0','1','6','0','0'),
('181','23','28','fieldtype_created_by','',NULL,'0',NULL,'0',NULL,NULL,'0','1','7','0','0'),
('182','23','28','fieldtype_grouped_users','部門',NULL,'0',NULL,'1',NULL,NULL,'0','1','4','1','0'),
('179','23','28','fieldtype_id','',NULL,'0',NULL,'0',NULL,NULL,'0','1','1','0','0'),
('163','22','26','fieldtype_action','',NULL,'0',NULL,'0',NULL,NULL,'0','1','0','0','0'),
('162','21','24','fieldtype_attachments','添付ファイル',NULL,'0',NULL,'0',NULL,NULL,'5','0','0','0','0'),
('161','21','25','fieldtype_users','チーム',NULL,'0',NULL,'0',NULL,'{\"display_as\":\"checkboxes\"}','0','0','0','0','0'),
('159','21','24','fieldtype_input_date','開始日',NULL,'0',NULL,'0',NULL,'{\"background\":\"\"}','3','1','5','0','0'),
('160','21','24','fieldtype_textarea_wysiwyg','説明',NULL,'0',NULL,'0',NULL,'{\"allow_search\":\"1\"}','4','0','0','0','0'),
('158','21','24','fieldtype_input','名前',NULL,'1',NULL,'1',NULL,'{\"allow_search\":\"1\",\"width\":\"input-xlarge\"}','2','1','3','0','0'),
('183','23','28','fieldtype_dropdown','種別',NULL,'0',NULL,'1',NULL,'{\"width\":\"input-large\",\"use_search\":\"0\"}','2','1','2','1','1'),
('175','22','27','fieldtype_input_date','開始日',NULL,'0',NULL,'0',NULL,'{\"background\":\"\"}','3','0','0','0','0'),
('157','21','24','fieldtype_dropdown','ステータス',NULL,'0',NULL,'1',NULL,'{\"width\":\"input-medium\",\"use_search\":\"0\"}','1','1','4','1','1'),
('156','21','24','fieldtype_dropdown','優先度',NULL,'0',NULL,'1',NULL,'{\"width\":\"input-medium\",\"use_search\":\"0\"}','0','1','2','1','0'),
('155','21','24','fieldtype_created_by','',NULL,'0',NULL,'0',NULL,NULL,'0','1','7','0','0'),
('154','21','24','fieldtype_date_added','',NULL,'0',NULL,'0',NULL,NULL,'0','1','6','0','0'),
('153','21','24','fieldtype_id','',NULL,'0',NULL,'0',NULL,NULL,'0','1','1','0','0'),
('152','21','24','fieldtype_action','',NULL,'0',NULL,'0',NULL,NULL,'0','1','0','0','0'),
('185','23','28','fieldtype_textarea_wysiwyg','説明',NULL,'0',NULL,'0',NULL,'{\"allow_search\":\"1\"}','4','0','0','0','0'),
('186','23','28','fieldtype_dropdown','ステータス',NULL,'0',NULL,'1',NULL,'{\"width\":\"input-large\",\"use_search\":\"0\"}','1','1','5','1','2'),
('187','24','29','fieldtype_action','',NULL,'0',NULL,'0',NULL,NULL,'0','1','0','0','0'),
('188','24','29','fieldtype_id','',NULL,'0',NULL,'0',NULL,NULL,'0','1','1','0','0'),
('189','24','29','fieldtype_date_added','',NULL,'0',NULL,'0',NULL,NULL,'0','1','4','0','0'),
('190','24','29','fieldtype_created_by','',NULL,'0',NULL,'0',NULL,NULL,'0','1','5','0','0'),
('191','24','29','fieldtype_input','名前',NULL,'1',NULL,'1',NULL,'{\"allow_search\":\"1\",\"width\":\"input-xlarge\"}','1','1','3','0','0'),
('192','24','29','fieldtype_textarea_wysiwyg','説明',NULL,'0',NULL,'0',NULL,'{\"allow_search\":\"1\"}','2','0','0','0','0'),
('193','24','29','fieldtype_dropdown','ステータス',NULL,'0',NULL,'0',NULL,'{\"width\":\"input-medium\",\"use_search\":\"0\"}','0','1','2','1','0'),
('194','23','28','fieldtype_attachments','添付ファイル',NULL,'0',NULL,'0',NULL,NULL,'5','0','0','0','0'),
('195','24','29','fieldtype_attachments','添付ファイル',NULL,'0',NULL,'0',NULL,NULL,'3','0','0','0','0');

CREATE TABLE `app_fields_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_groups_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `access_schema` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_access_groups_id` (`access_groups_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

CREATE TABLE `app_fields_choices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `fields_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  `bg_color` varchar(16) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `users` text,
  PRIMARY KEY (`id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;

INSERT INTO app_fields_choices VALUES
('35','0','156','高','0',NULL,'2',NULL),
('34','0','156','緊急','0',NULL,'1',NULL),
('37','0','157','NEW','0',NULL,'1',NULL),
('38','0','157','オープン','0',NULL,'2',NULL),
('39','0','157','待ち','0',NULL,'3',NULL),
('40','0','157','クローズ','0',NULL,'4',NULL),
('41','0','157','キャンセル','0',NULL,'5',NULL),
('42','0','167','タスク','1',NULL,'1',NULL),
('43','0','167','変更','0',NULL,'2',NULL),
('44','0','167','バグ','0','#ff7a00','3',NULL),
('45','0','167','アイデア','0',NULL,'0',NULL),
('46','0','169','新規','1',NULL,'0',NULL),
('47','0','169','オープン','0',NULL,'2',NULL),
('48','0','169','保留','0',NULL,'3',NULL),
('49','0','169','済み','0',NULL,'4',NULL),
('50','0','169','クローズ','0',NULL,'5',NULL),
('51','0','169','支払い済み','0',NULL,'6',NULL),
('52','0','169','キャンセル','0',NULL,'7',NULL),
('53','0','170','緊急','0','#ff0000','1',NULL),
('54','0','170','高','0',NULL,'2',NULL),
('55','0','170','低','1',NULL,'3',NULL),
('56','0','182','サポート部門','0',NULL,'0','21,20'),
('57','0','183','変更リクエスト','0',NULL,'1',NULL),
('58','0','183','バグレポート','0',NULL,'2',NULL),
('59','0','183','質問','0',NULL,'3',NULL),
('60','0','186','NEW','1',NULL,'0',NULL),
('61','0','186','オープン','0',NULL,'2',NULL),
('62','0','186','顧客待ち','0',NULL,'3',NULL),
('63','0','186','クローズ','0',NULL,'4',NULL),
('64','0','186','キャンセル','0',NULL,'5',NULL),
('65','0','193','オープン','0',NULL,'1',NULL),
('66','0','193','クローズ','0',NULL,'2',NULL),
('67','0','193','新規','1',NULL,'0',NULL);

CREATE TABLE `app_forms_tabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

INSERT INTO app_forms_tabs VALUES
('1','1','Info','','0'),
('25','21','チーム','','3'),
('26','22','基本情報','','2'),
('27','22','時間','','3'),
('28','23','Info','','0'),
('29','24','Info','','0'),
('24','21','基本情報','','2');

CREATE TABLE `app_related_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `related_entities_id` int(11) NOT NULL,
  `related_items_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_related_entities_id` (`related_entities_id`),
  KEY `idx_related_items_id` (`related_items_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `app_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `entities_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `reports_type` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `in_menu` tinyint(1) NOT NULL DEFAULT '0',
  `in_dashboard` tinyint(4) NOT NULL DEFAULT '0',
  `dashboard_sort_order` int(11) DEFAULT NULL,
  `listing_order_fields` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;

INSERT INTO app_reports VALUES
('59','0','21','0','default','','0','0',NULL,''),
('61','0','22','0','default','','0','0',NULL,''),
('63','0','23','0','default','','0','0',NULL,''),
('66','0','21','19','entity','','0','0',NULL,''),
('67','0','1','19','entity','','0','0',NULL,''),
('68','0','1','0','default','','0','0',NULL,''),
('69','0','24','0','default','','0','0',NULL,''),
('70','0','21','20','entity','','0','0',NULL,''),
('71','0','22','20','entity','','0','0',NULL,''),
('72','0','21','21','entity','','0','0',NULL,''),
('73','0','22','21','entity','','0','0',NULL,''),
('74','0','23','21','entity','','0','0',NULL,''),
('75','0','24','21','entity','','0','0',NULL,'');

CREATE TABLE `app_reports_filters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reports_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `filters_values` text NOT NULL,
  `filters_condition` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reports_id` (`reports_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=utf8;

INSERT INTO app_reports_filters VALUES
('70','61','169','46,47,48','include'),
('68','59','157','37,38,39','include'),
('72','63','186','60,61,62','include'),
('73','66','157','37,38,39','include'),
('74','70','157','37,38,39','include'),
('75','71','169','46,47,48','include'),
('76','72','157','37,38,39','include'),
('77','73','169','46,47,48','include'),
('78','74','186','60,61,62','include');

CREATE TABLE `app_sessions` (
  `sesskey` varchar(32) NOT NULL,
  `expiry` int(11) unsigned NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`sesskey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO app_sessions VALUES
('c329013b92bbd6978ab8106ae894faa3','1441941855','uploadify_attachments|a:0:{}alerts|O:6:\"alerts\":1:{s:8:\"messages\";a:0:{}}app_send_to|N;app_current_version|s:3:\"1.5\";app_selected_items|a:1:{i:66;a:0:{}}app_logged_users_id|s:2:\"19\";plugin_ext_current_version|s:3:\"1.1\";'),
('67a4d185205807a7d3a541e812678138','1441957894','uploadify_attachments|a:0:{}alerts|O:6:\"alerts\":1:{s:8:\"messages\";a:0:{}}app_send_to|N;app_selected_items|a:0:{}plugin_ext_current_version|s:3:\"1.1\";app_current_version|s:3:\"1.5\";app_logged_users_id|s:2:\"19\";');

CREATE TABLE `app_users_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `configuration_name` varchar(255) NOT NULL DEFAULT '',
  `configuration_value` text,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

