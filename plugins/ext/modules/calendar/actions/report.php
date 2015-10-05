<?php

//check if report exist
$reports_query = db_query("select * from app_ext_calendar where id='" . db_input($_GET['id']) . "'");
if(!$reports = db_fetch_array($reports_query))
{
  redirect_to('dashboard/page_not_found');
}

if(!calendar::user_has_reports_access($reports))
{
  redirect_to('dashboard/access_forbidden');
}

//create default entity report for logged user
$reports_info_query = db_query("select * from app_reports where entities_id='" . db_input($reports['entities_id']). "' and reports_type='calendarreport" . $reports['id']. "' and created_by='" . $app_logged_users_id . "'");
if(!$reports_info = db_fetch_array($reports_info_query))
{
  $sql_data = array('name'=>'',
                   'entities_id'=>$reports['entities_id'],
                   'reports_type'=>'calendarreport' . $reports['id'],                                              
                   'in_menu'=>0,
                   'in_dashboard'=>0,
                   'listing_order_fields'=>'',
                   'created_by'=>$app_logged_users_id,
                   );
                   
  db_perform('app_reports',$sql_data);
  $fiters_reports_id = db_insert_id();  
}
else
{
  $fiters_reports_id = $reports_info['id'];
}


$heading_field_id = fields::get_heading_id($reports['entities_id']);

if(!$heading_field_id)
{
  $alerts->add(TEXT_ERROR_NO_HEADING_FIELD,'warning');  
}

switch($app_module_action)
{ 
  case 'resize':
      if(strstr($_POST['end'],'T'))
      {
        $end = get_date_timestamp($_POST['end']);        
      }
      else
      {
        $end = strtotime('-1 day',get_date_timestamp($_POST['end']));                                                  
      }
        
      $sql_data = array('field_' . $reports['end_date'] =>$end);
      
      db_perform("app_entity_" . $reports['entities_id'] ,$sql_data,'update',"id='" . db_input($_POST['id']) . "'");
  
      exit();
    break;
  case 'drop':
  
      if(isset($_POST['end']))
      {        
        if(strstr($_POST['end'],'T'))
        {
          $end = get_date_timestamp($_POST['end']);
        }
        else
        {
          $end = strtotime('-1 day',get_date_timestamp($_POST['end']));        
        }
      }
      else
      {
        $end = get_date_timestamp($_POST['start']);
      }
      
      $sql_data = array('field_' . $reports['start_date']=>get_date_timestamp($_POST['start']),
                        'field_' . $reports['end_date']=>$end);
                        
      db_perform("app_entity_" . $reports['entities_id'],$sql_data,'update',"id='" . db_input($_POST['id']) . "'");
        
      exit();
    break;
  case 'get_events':        
      $list = array();
      
      $cfg_editable = calendar::user_has_reports_access($reports,'full');
      
      $date_from = $_GET['start'];
      $date_to = $_GET['end'];
      
      $listing_sql_query = " and ( (FROM_UNIXTIME(e.field_" . $reports['start_date'] . ",'%Y-%m-%d')>='" . $date_from . "' and  FROM_UNIXTIME(e.field_" . $reports['end_date'] . ",'%Y-%m-%d')<='" . $date_to . "') or 
                           (FROM_UNIXTIME(e.field_" . $reports['start_date'] . ",'%Y-%m-%d')<'" . $date_from . "' and  FROM_UNIXTIME(e.field_" . $reports['end_date'] . ",'%Y-%m-%d')>'" . $date_to . "') or
                           (FROM_UNIXTIME(e.field_" . $reports['start_date'] . ",'%Y-%m-%d')<'" . $date_from . "' and  FROM_UNIXTIME(e.field_" . $reports['end_date'] . ",'%Y-%m-%d')<='" . $date_to . "' and  FROM_UNIXTIME(e.field_" . $reports['end_date'] . ",'%Y-%m-%d')>='" . $date_from . "') or
                           (FROM_UNIXTIME(e.field_" . $reports['start_date'] . ",'%Y-%m-%d')>='" . $date_from . "' and FROM_UNIXTIME(e.field_" . $reports['start_date'] . ",'%Y-%m-%d')<='" . $date_to . "' and  FROM_UNIXTIME(e.field_" . $reports['end_date'] . ",'%Y-%m-%d')>'" . $date_to . "') 
                           ) ";   
         
         
      $listing_sql_query = reports::add_filters_query($fiters_reports_id,$listing_sql_query);   
                           
      if(isset($_GET['path']))
      {
        $path_info = items::parse_path($_GET['path']);
        if($path_info['parent_entity_item_id']>0)
        {
          $listing_sql_query .= " and e.parent_item_id='" . $path_info['parent_entity_item_id'] . "'";
        }
      }  
      
      $listing_sql_query = items::add_access_query($reports['entities_id'],$listing_sql_query);                         
                      
      $events_query = db_query("select e.* from app_entity_" . $reports['entities_id'] . " e where length(e.field_" . $reports['start_date'] . ")>0 and length(e.field_" . $reports['end_date'] . ")>0 " . $listing_sql_query);
      while($events = db_fetch_array($events_query))
      {
        $start = date('Y-m-d H:i',$events['field_' . $reports['start_date']]);
        $end = date('Y-m-d H:i',$events['field_' . $reports['end_date']]);
        
        if(strstr($end,' 00:00'))
        {
          $end = date('Y-m-d H:i',strtotime('+1 day',$events['field_' . $reports['end_date']]));
        }
         
        $list[] = array('id' => $events['id'],
                        'title' => $events['field_' . $heading_field_id],                        
                        'start' => str_replace(' 00:00','',$start),
                        'end' => str_replace(' 00:00','',$end),                           
                        'editable'=>$cfg_editable,                   
                        'allDay'=>(strstr($start,'00:00') and strstr($end,'00:00')),
                        'url' => url_for('items/info','path=' . $reports['entities_id'] . '-' . $events['id']),                                              
                        );      
      }
            
      echo json_encode($list);
          
      exit();  
      
    break;
}    
