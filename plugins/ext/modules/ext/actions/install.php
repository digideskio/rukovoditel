<?php

if(!defined('CFG_PLUGIN_EXT_INSTALLED'))
{
  $install_sql ='
    CREATE TABLE IF NOT EXISTS `app_ext_calendar` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `entities_id` int(11) NOT NULL,
      `name` varchar(64) NOT NULL,
      `start_date` int(11) NOT NULL,
      `end_date` int(11) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `idx_entities_id` (`entities_id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
    
    CREATE TABLE IF NOT EXISTS `app_ext_calendar_access` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `calendar_id` int(11) DEFAULT NULL,
      `calendar_type` varchar(16) NOT NULL,
      `access_groups_id` int(11) NOT NULL,
      `access_schema` varchar(64) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `idx_calendar_id` (`calendar_id`),
      KEY `idx_access_groups_id` (`access_groups_id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ;
    
    CREATE TABLE IF NOT EXISTS `app_ext_calendar_events` (
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
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
    
    CREATE TABLE IF NOT EXISTS `app_ext_ganttchart` (
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
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
    
    
    CREATE TABLE IF NOT EXISTS `app_ext_ganttchart_access` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `ganttchart_id` int(11) NOT NULL,
      `access_groups_id` int(11) NOT NULL,
      `access_schema` varchar(64) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `idx_ganttchart_id` (`ganttchart_id`),
      KEY `idx_access_groups_id` (`access_groups_id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
    
    CREATE TABLE IF NOT EXISTS `app_ext_ganttchart_depends` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `ganttchart_id` int(11) NOT NULL,
      `item_id` int(11) NOT NULL,
      `depends_id` int(11) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `idx_item_id` (`item_id`),
      KEY `idx_depends_id` (`depends_id`),
      KEY `idx_ganttchart_id` (`ganttchart_id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
    
    CREATE TABLE IF NOT EXISTS `app_ext_graphicreport` (
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
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
  ';

  foreach(explode(';',$install_sql) as $query)
  {
    if(strlen(trim($query))>0)
    {
      db_query(trim($query));
    }
  }

  db_perform('app_configuration',array('configuration_value'=>1,'configuration_name'=>'CFG_PLUGIN_EXT_INSTALLED'));
  
  $alerts->add(TEXT_EXT_PLUGIN_INSTALLED,'success');
  
  redirect_to('ext/license/key');
}