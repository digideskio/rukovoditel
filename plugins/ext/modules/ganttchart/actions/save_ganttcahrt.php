<?php

//check if report exist
$reports_query = db_query("select * from app_ext_ganttchart where id='" . db_input($_GET['id']) . "'");
if(!$reports = db_fetch_array($reports_query))
{
  exit();
}

if(!ganttchart::users_has_full_access($reports))
{
  exit();
}

$heading_field_id = fields::get_heading_id($reports['entities_id']);

if(!$heading_field_id)
{
  exit();
}

$ganttcahrt_data = $_POST['ganttcahrt_data'];

if($ganttcahrt_data = json_decode($ganttcahrt_data,true))
{
  $items = $ganttcahrt_data['tasks'];
  
  //print_r($items);
  
  //assign item id to row number
  $rows_to_id = array();
  $row = 1;
  foreach($items as $k=>$v)
  {
    if($k>0)
    {
      if(strstr($v['id'],'tmp'))
      {
        $sql_data = array();
        $sql_data['field_' . $heading_field_id] = $v['name'];
        $sql_data['date_added'] = time();
        $sql_data['created_by'] = $app_logged_users_id;
        
        if(strlen($_POST['path'])>0)
        {
          $sql_data['parent_item_id'] = items::get_paretn_entity_item_id_by_path($_POST['path']);
        }
        else
        {
          $sql_data['parent_item_id'] = 0;
        }
                
        db_perform('app_entity_' . $reports['entities_id'],$sql_data);
        $rows_to_id[$row] = db_insert_id();
      }
      else
      {
        $rows_to_id[$row] = $v['id'];
      }
    }
    
    $row++;  
  }
  
  //reset depends
  db_query("delete from app_ext_ganttchart_depends where ganttchart_id='" . $reports['id']. "'");
      
  //update items
  $row = 1;
  foreach($items as $k=>$v)
  {
    if($k>0)
    {
      //+32400000 to start date... not sure why...
      $v['start']+=32400000;      
  
      $sql_data = array();
      $sql_data['field_' . $reports['start_date']] = ($v['start']/1000);
      $sql_data['field_' . $reports['end_date']] = ($v['end']/1000);
      $sql_data['field_' . $heading_field_id] = $v['name'];
      $sql_data['sort_order'] = $row;
      
      db_perform('app_entity_' . $reports['entities_id'],$sql_data,'update',"id='" . db_input($rows_to_id[$row]) . "'");
      
      if(strlen($v['depends'])>0)
      {
        foreach(explode(',',$v['depends']) as $depended_row)
        {        
          $sql_data = array();
          $sql_data['item_id'] = $rows_to_id[$row];
          $sql_data['depends_id'] = $rows_to_id[$depended_row];
          $sql_data['ganttchart_id'] = $reports['id'];
                         
          db_perform('app_ext_ganttchart_depends',$sql_data);
        }
      }
      
      //print_r($sql_data);
    }
  
    $row++;
  }
}
        
exit();