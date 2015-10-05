<?php

class ganttchart
{
  static public function get_access_by_report($ganttchart_id,$groups_id)
  {
    $info_query = db_query("select * from app_ext_ganttchart_access where ganttchart_id='" . db_input($ganttchart_id) . "' and access_groups_id='" . db_input($groups_id) . "'");
    if($info = db_fetch_array($info_query))
    {
      return $info['access_schema'];
    }
    else
    {
      return '';
    }
  }
  
  
  static public function users_has_access($ganttchart_id)
  {
    global $app_user;
    
    if($app_user['group_id']==0) return true;
    
    $info_query = db_query("select * from app_ext_ganttchart_access where ganttchart_id='" . db_input($ganttchart_id) . "' and access_groups_id='" . db_input($app_user['group_id']) . "'");
    if($info = db_fetch_array($info_query))
    {
      return true;
    }
    else
    {
      return false;
    }
  } 
  
  static public function users_has_full_access($reports)
  {
    global $app_user;
    
    if($app_user['group_id']==0) return true;
    
    $info_query = db_query("select * from app_ext_ganttchart_access where ganttchart_id='" . db_input($reports['id']) . "' and access_groups_id='" . db_input($app_user['group_id']) . "' and access_schema='full'");
    if($info = db_fetch_array($info_query))
    {  
      $access_schema = users::get_entities_access_schema($reports['entities_id'],$app_user['group_id']);
          
      if(users::has_access('view_assigned',$access_schema) and $app_user['group_id']>0)
      {
        return false;
      }
      else
      {      
        return true;
      }
    }
    else
    {
      return false;
    }
  } 
  
}