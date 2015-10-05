<?php

//check if report exist
$reports_query = db_query("select * from app_ext_graphicreport where id='" . db_input($_GET['id']) . "'");
if(!$reports = db_fetch_array($reports_query))
{
  redirect_to('dashboard/page_not_found');
}

if(!in_array($app_user['group_id'],explode(',',$reports['allowed_groups'])) and $app_user['group_id']>0)
{
  redirect_to('dashboard/access_forbidden');
}


//create default entity report for logged user
$reports_info_query = db_query("select * from app_reports where entities_id='" . db_input($reports['entities_id']). "' and reports_type='graphicreport" . $reports['id']. "' and created_by='" . $app_logged_users_id . "'");
if(!$reports_info = db_fetch_array($reports_info_query))
{
  $sql_data = array('name'=>'',
                   'entities_id'=>$reports['entities_id'],
                   'reports_type'=>'graphicreport' . $reports['id'],                                              
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

//get report entity info
$entity_info = db_find('app_entities',$reports_info['entities_id']);
  
//check if parent reports was not set
if($entity_info['parent_id']>0 and $reports_info['parent_id']==0)
{
  reports::auto_create_parent_reports($reports_info['id']);
}

if (!app_session_is_registered('graphicreport_filters')) 
{
  $graphicreport_filters = array();
  app_session_register('graphicreport_filters');    
}



//set chart type
if(isset($_GET['chart_type']))
{
  $graphicreport_filters[$reports['id']]['chart_type'] = $_GET['chart_type'];
}
elseif(!isset($graphicreport_filters[$reports['id']]['chart_type']))
{
  $graphicreport_filters[$reports['id']]['chart_type'] = $reports["chart_type"];
}

//set chart period
if(isset($_GET['period']))
{
  $graphicreport_filters[$reports['id']]['period'] = $_GET['period'];
}
elseif(!isset($graphicreport_filters[$reports['id']]['period']))
{
  $graphicreport_filters[$reports['id']]['period'] = $reports["period"];
}

//set filter by year
if(isset($_GET['year_filter']))
{
  $graphicreport_filters[$reports['id']]['year_filter'] = $_GET['year_filter'];
}
elseif(!isset($graphicreport_filters[$reports['id']]['year_filter']))
{
  $graphicreport_filters[$reports['id']]['year_filter'] = date('Y');
}

//set filter by month
if(isset($_GET['month_filter']))
{
  $graphicreport_filters[$reports['id']]['month_filter'] = $_GET['month_filter'];
}
elseif(!isset($graphicreport_filters[$reports['id']]['month_filter']))
{
  $graphicreport_filters[$reports['id']]['month_filter'] = date('n');  
} 

$chart_type = $graphicreport_filters[$reports['id']]['chart_type'];
$period = $graphicreport_filters[$reports['id']]['period']; 
$year_filter = $graphicreport_filters[$reports['id']]['year_filter']; 
$month_filter = $graphicreport_filters[$reports['id']]['month_filter'];

//generate year filter list
$xaxis_field_info = db_find('app_fields',$reports['xaxis']);
if($xaxis_field_info['type']=='fieldtype_date_added')
{  
  $xaxis_sql_name = "e.date_added";
}
else
{
  $xaxis_sql_name = "e.field_" . $reports['xaxis']; 
}

$listing_sql = "select max(" . $xaxis_sql_name . ") as max_date, min(" . $xaxis_sql_name . ") as min_date from app_entity_" . $reports['entities_id'] . " e  where length(" . $xaxis_sql_name . ")>0 limit 1";

$items_query = db_query($listing_sql);
if($items = db_fetch_array($items_query))
{  
  $years_list = array();
  for($i=date('Y',$items['min_date']);$i<=date('Y',$items['max_date']);$i++)
  {
    $year = ($i==$year_filter ? '<b>' . $i . '</b>':$i);
    $years_list[] = '<a href="' . url_for('ext/graphicreport/view','id=' . $_GET['id'] . '&year_filter=' . $i . '&month_filter=' . $month_filter) . '">' . $year . '</a>';
  }
}
else
{
  $years_list = array(date('Y')=>date('Y'));
}

//generate month filter list
$months_list = array();
$months_array = explode(',',str_replace('"','',TEXT_DATEPICKER_MONTHS)); 
foreach($months_array  as $k=>$v)
{
  $v = ($month_filter==($k+1) ? '<b>' . $v. '</b>':$v);
  
  $months_list[]= '<a href="' . url_for('ext/graphicreport/view','id=' . $_GET['id'] . '&year_filter=' . $year_filter . '&month_filter=' . ($k+1)) . '">' . $v . '</a>';
}

/**
 *start build listing query
 */ 
 
$listing_sql_query = '';
$select_sql_query = '';

$listing_sql_query = reports::add_filters_query($fiters_reports_id,$listing_sql_query);

$listing_sql_query = items::add_access_query($reports['entities_id'],$listing_sql_query);

if($period=='daily')
{
  $listing_sql_query .= " and date_format(FROM_UNIXTIME(" . $xaxis_sql_name . "),'%Y-%c')='" . $year_filter ."-" . $month_filter. "'";
  $select_sql_query .= " date_format(FROM_UNIXTIME(" . $xaxis_sql_name . "),'%Y-%m-%d') as xaxis";     
}
elseif($period=='monthly')
{
  $listing_sql_query .= " and date_format(FROM_UNIXTIME(" . $xaxis_sql_name . "),'%Y')='" . $year_filter . "'";
  $select_sql_query .= " date_format(FROM_UNIXTIME(" . $xaxis_sql_name . "),'%Y-%m') as xaxis";     
}
elseif($period=='yearly')
{
  $listing_sql_query .= " ";
  $select_sql_query .= " date_format(FROM_UNIXTIME(" . $xaxis_sql_name . "),'%Y') as xaxis";     
}


$fields_names = array();
$sum_sql = array();
foreach(explode(',',$reports['yaxis']) as $id)
{
  $sum_sql[] = "sum(e.field_" . $id . ") as sum_field_" . $id;
  
  $finfo = db_find('app_fields',$id);
  $fields_names[$id] = $finfo['name'];
}

$xaxis = array();
$yaxis = array();
$listing_sql = "select " . $xaxis_sql_name . " as xaxis_field, " . $select_sql_query . ", " . implode(',',$sum_sql) . " from app_entity_" . $reports['entities_id'] . " e  where length(" . $xaxis_sql_name . ")>0 " . $listing_sql_query . " group by xaxis order by " . $xaxis_sql_name ;
$items_query = db_query($listing_sql);
while($item = db_fetch_array($items_query))
{ 
  if($period=='daily')
  { 
    $xaxis[] = "'" . format_date($item['xaxis_field']) . "'";
  }
  elseif($period=='monthly')
  {
    $xaxis[] = "'" . i18n_date('F Y',$item['xaxis_field']) . "'";
  }
  elseif($period=='yearly')
  {
    $xaxis[] = "'" . i18n_date('Y', $item['xaxis_field']) . "'";
  }
  
  
  foreach(explode(',',$reports['yaxis']) as $id)
  {
    $yaxis[$fields_names[$id]][] = number_format((float)$item['sum_field_' . $id],2);
  } 
}

$yaxis_html = array();

foreach($yaxis as $name=>$data)
{
  $yaxis_html[] = '{name:"' . addslashes($name). '",data:[' . implode(',',$data). ']}';
}
  