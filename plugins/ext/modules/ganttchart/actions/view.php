<?php

//check if report exist
$reports_query = db_query("select * from app_ext_ganttchart where id='" . db_input($_GET['id']) . "'");
if(!$reports = db_fetch_array($reports_query))
{
  redirect_to('dashboard/page_not_found');
}

if(!ganttchart::users_has_access($reports['id']))
{
  redirect_to('dashboard/access_forbidden');
}

$heading_field_id = fields::get_heading_id($reports['entities_id']);

if(!$heading_field_id)
{
  $alerts->add(TEXT_ERROR_NO_HEADING_FIELD,'warning');  
}



$fields_access_schema = users::get_fields_access_schema($reports['entities_id'],$app_user['group_id']);
$entity_info = db_find('app_entities',$reports['entities_id']);

$ganttcahrt_data = array();

$listing_sql_query = '';

if(isset($_GET['path']))
{
  $path_info = items::parse_path($_GET['path']);
  if($path_info['parent_entity_item_id']>0)
  {
    $listing_sql_query .= " and e.parent_item_id='" . $path_info['parent_entity_item_id'] . "'";
  }
}  

//check view assigned only access
$listing_sql_query = items::add_access_query($reports['entities_id'],$listing_sql_query);

$listing_sql = "select e.* from app_entity_" . $reports['entities_id'] . " e  where length(e.field_" . $reports['start_date'] . ")>0 and length(e.field_" . $reports['end_date'] . ")>0 " . $listing_sql_query . " order by e.sort_order, e.field_" . $reports['start_date'] . "";

$id_to_row = array();
$row = 2;
$items_query = db_query($listing_sql);
while($item = db_fetch_array($items_query))
{ 
  $id_to_row[$item['id']] = $row;
  
  $row++;
}

$items_query = db_query($listing_sql);
while($item = db_fetch_array($items_query))
{  
  $start_date = $item['field_' . $reports['start_date']];
  $end_date = $item['field_' . $reports['end_date']];
  
  if(!isset($min_start_date))
  {
    $min_start_date = $start_date;
  }
  elseif($start_date<$min_start_date)
  {
    $min_start_date = $start_date;
  }
  
  if(!isset($max_end_date))
  {
    $max_end_date = $end_date; 
  }
  elseif($end_date>$max_end_date)
  {
    $max_end_date = $end_date;
  }
     
  $start = (float)number_format($start_date*1000,0,'.','');
  $end = (float)number_format($end_date*1000,0,'.','');   
  
  /*
  $weekends = explode(',',$reports['weekends']);
  $key = array_search(0,$weekends);
  if($key!==false)
  {
    $weekends[$key] = 7;
  } 
  */   
      
  $duration = day_diff($start_date,$end_date)+1;
        
  $data  = array('id'  => (int)$item['id'],
                  'name' => ($heading_field_id>0 ? $item['field_' . $heading_field_id] : ''),
                  'code' => '',
                  'level'=> 1,
                  'status' => 'STATUS_ACTIVE',
                  'canWrite' => true,
                  'start' => $start,
                  'duration' => $duration,
                  'end' => $end,
                  'startIsMilestone' => false,
                  'endIsMilestone' => false,
                  'collapsed' => false,
                  'assigs' => array(),
                  'description'=>'',                                                        
                  'progress' =>(isset($item['field_' . $reports['progress']]) ? $item['field_' . $reports['progress']]:0),                                      
                  'hasChild' => false,
                  );
  
  //add fields in listing                
  if(strlen($reports['fields_in_listing'])>0)
  {                  
    $fields_query = db_query("select * from app_fields where entities_id='" . $reports['entities_id'] . "' and id in (" . $reports['fields_in_listing']  . ") order by field(id," . $reports['fields_in_listing'] . ")");
    while($field = db_fetch_array($fields_query))
    {
    
      //check field access
      if(isset($fields_access_schema[$field['id']]))
      {
        if($fields_access_schema[$field['id']]=='hide') continue;
      }
      
      switch($field['type'])
      {
        case 'fieldtype_created_by':
            $value = $item['created_by'];
          break;
        case 'fieldtype_date_added':
            $value = $item['date_added'];                
          break;
        case 'fieldtype_action':                
        case 'fieldtype_id':
            $value = $item['id'];
          break;
        default:
            $value = $item['field_' . $field['id']]; 
          break;
      }    
      
      $output_options = array('class'=>$field['type'],
                              'value'=>$value,
                              'field'=>$field,
                              'item'=>$item,
                              'choices_cache'=>$app_choices_cache,
                              'users_cache'=>$app_users_cache);
                                   
                            
      $data['field_' . $field['id']] = strip_tags(fields_types::output($output_options));
    }
  }
  
  //add depends
  $depends_list = array();
  $depends_query = db_query("select * from app_ext_ganttchart_depends where ganttchart_id='" . db_input($reports['id']). "' and item_id='" . db_input($item['id']) . "'");
  while($depends = db_fetch_array($depends_query))
  {
    $depends_list[] = $id_to_row[$depends['depends_id']];
  }   
  
  if(count($depends_list)>0)
  {
    $data['depends'] = implode(',',$depends_list);
    $data['status'] = 'STATUS_SUSPENDED';
  }               
                                      
  $ganttcahrt_data['tasks'][] = $data;
                                          
}

//echo '<pre>';
//print_r($ganttcahrt_data);

$start = (float)number_format($min_start_date*1000,0,'.','');
$end = (float)number_format($max_end_date*1000,0,'.','');

$duration = day_diff($min_start_date,$max_end_date)+1;

$milestone = array(array('id'  => 0,
                    'name' => entities::get_listing_heading($reports['entities_id']),
                    'code' => '',
                    'level'=> 0,
                    'status' => 'STATUS_ACTIVE',
                    'canWrite' => true,
                    'start' => $start,
                    'duration' => $duration,
                    'end' => $end,
                    'startIsMilestone' => false,
                    'endIsMilestone' => false,
                    'collapsed' => false,
                    'assigs' => array(),
                    'description'=>'',
                    'progress' =>'',
                    'hasChild' => false,
                    ));

$users_has_full_access = ganttchart::users_has_full_access($reports);
$ganttcahrt_data['tasks'] = array_merge($milestone,$ganttcahrt_data['tasks']);                    
$ganttcahrt_data['selectedRow'] = 0;
$ganttcahrt_data['canWrite'] = $users_has_full_access;
$ganttcahrt_data['canWriteOnParent'] = true;


     

