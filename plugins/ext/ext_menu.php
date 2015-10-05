<?php

$app_plugin_menu['extension'][] = array('title'=>TEXT_EXT_GANTTCHART_REPORT,'url'=>url_for('ext/ganttchart/configuration'));

$s = array();
$s[] = array('title'=>TEXT_EXT_СALENDAR_PERSONAL,'url'=>url_for('ext/calendar/configuration_personal')); 
$s[] = array('title'=>TEXT_EXT_СALENDAR_PUBLIC,'url'=>url_for('ext/calendar/configuration_public'));
$s[] = array('title'=>TEXT_EXT_СALENDAR_REPORTS,'url'=>url_for('ext/calendar/configuration_reports'));
$app_plugin_menu['extension'][] = array('title'=>TEXT_EXT_СALENDAR,'url'=>url_for('ext/calendar/configuration_personal'),'submenu'=>$s);

$app_plugin_menu['extension'][] = array('title'=>TEXT_EXT_GRAPHIC_REPORT,'url'=>url_for('ext/graphicreport/configuration'));
$app_plugin_menu['extension'][] = array('title'=>TEXT_EXT_LICENSE,'url'=>url_for('ext/license/key'));
//$app_plugin_menu['extension'][] = array('title'=>TEXT_EXT_SUMMARY_REPORT,'url'=>url_for('ext/summary/configuration'));


/**
 *add gantt reports to menu
 */ 
if($app_user['group_id']>0)
{
  $reports_query = db_query("select g.* from app_ext_ganttchart g, app_entities e, app_ext_ganttchart_access ga where e.id=g.entities_id and e.parent_id=0 and g.id=ga.ganttchart_id and ga.access_groups_id='" . db_input($app_user['group_id']) . "' order by name");
}
else
{
  $reports_query = db_query("select g.* from app_ext_ganttchart g, app_entities e where e.id=g.entities_id and e.parent_id=0 order by g.name");
}

while($reports = db_fetch_array($reports_query))
{  
  $app_plugin_menu['reports'][] = array('title'=>$reports['name'],'url'=>url_for('ext/ganttchart/view','id=' . $reports['id']));  
}


/**
 *add gantt reports to items menu
 */ 
if(isset($_GET['path']))
{
  $entities_list = items::get_sub_entities_list_by_path($_GET['path']);
  
  if(count($entities_list))
  {        
    if($app_user['group_id']>0)
    {
      $reports_query = db_query("select g.* from app_ext_ganttchart g, app_entities e, app_ext_ganttchart_access ga where e.id=g.entities_id and e.id in (" . implode(',',$entities_list) . ") and g.id=ga.ganttchart_id and ga.access_groups_id='" . db_input($app_user['group_id']) . "' order by name");
    }
    else
    {
      $reports_query = db_query("select g.* from app_ext_ganttchart g, app_entities e where e.id=g.entities_id and e.id in (" . implode(',',$entities_list) . ") order by g.name");
    }
    
    while($reports = db_fetch_array($reports_query))
    {  
      $app_plugin_menu['items_menu_reports'][] = array('title'=>$reports['name'],'url'=>url_for('ext/ganttchart/view','id=' . $reports['id'] . '&path=' . $_GET['path']));  
    }
  }
}

/**
 *add graphic reports to menu
 */ 
$reports_query = db_query("select * from app_ext_graphicreport order by name");
while($reports = db_fetch_array($reports_query))
{
  if(in_array($app_user['group_id'],explode(',',$reports['allowed_groups'])) or $app_user['group_id']==0)
  {
    $app_plugin_menu['reports'][] = array('title'=>$reports['name'],'url'=>url_for('ext/graphicreport/view','id=' . $reports['id']));
  }
}

/**
 *add personal calendar to user menu
 */ 
if(calendar::user_has_personal_access())
{
  $app_plugin_menu['account_menu'][] = array('title'=>TEXT_EXT_MY_СALENDAR,'url'=>url_for('ext/calendar/personal'),'class'=>'fa-calendar');    
} 


/**
 *add personal calendar to main menu
 */ 
if(calendar::user_has_public_access())
{
  $events = calendar::get_events(date('Y-m-d'),date('Y-m-d'),'public');

  if(($events_count = count($events))>0)
  {
    $app_plugin_menu['menu'][] = array('title'=>TEXT_EXT_СALENDAR,'url'=>url_for('ext/calendar/public'),'class'=>'fa-calendar','badge'=>'badge-info','badge_content'=>$events_count);
  }
  else
  {
    $app_plugin_menu['menu'][] = array('title'=>TEXT_EXT_СALENDAR,'url'=>url_for('ext/calendar/public'),'class'=>'fa-calendar');
  }    
} 

/**
 *add calendar reports to main menu
 */ 
 
if($app_user['group_id']>0)
{
  $reports_query = db_query("select c.* from app_ext_calendar c, app_entities e, app_ext_calendar_access ca where e.id=c.entities_id and e.parent_id=0 and c.id=ca.calendar_id and ca.access_groups_id='" . db_input($app_user['group_id']) . "' order by c.name");
}
else
{
  $reports_query = db_query("select c.* from app_ext_calendar c, app_entities e where e.id=c.entities_id and e.parent_id=0 order by c.name");
}

while($reports = db_fetch_array($reports_query))
{  
  $app_plugin_menu['reports'][] = array('title'=>$reports['name'],'url'=>url_for('ext/calendar/report','id=' . $reports['id']));  
} 

/**
 *add calendar reports to items menu
 */ 
if(isset($_GET['path']))
{
  $entities_list = items::get_sub_entities_list_by_path($_GET['path']);
  
  if(count($entities_list))
  {        
    if($app_user['group_id']>0)
    {
      $reports_query = db_query("select c.* from app_ext_calendar c, app_entities e, app_ext_calendar_access ca where e.id=c.entities_id and e.id in (" . implode(',',$entities_list) . ")  and c.id=ca.calendar_id and ca.access_groups_id='" . db_input($app_user['group_id']) . "' order by c.name");
    }
    else
    {
      $reports_query = db_query("select c.* from app_ext_calendar c, app_entities e where e.id=c.entities_id and  e.id in (" . implode(',',$entities_list) . ")  order by c.name");
    }
    
    while($reports = db_fetch_array($reports_query))
    {  
      $app_plugin_menu['items_menu_reports'][] = array('title'=>$reports['name'],'url'=>url_for('ext/calendar/report','id=' . $reports['id'] . '&path=' . $_GET['path']));  
    }
  }
}

