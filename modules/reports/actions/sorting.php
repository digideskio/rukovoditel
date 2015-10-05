<?php

$reports_info_query = db_query("select * from app_reports where id='" . db_input($_GET['reports_id']). "'");
$reports_info = db_fetch_array($reports_info_query);

$sorting_fields = array();
$sorting_fields_info = array();

if(strlen($reports_info['listing_order_fields'])>0)
{
  foreach(explode(',',$reports_info['listing_order_fields']) as $value)
  {
    $v = explode('_',$value);
    $sorting_fields[] = $v[0]; 
    $sorting_fields_info[$v[0]] = $v[1];
  }
}


switch($app_module_action)
{
  case 'set_sorting':                 
        $listing_order_fields = array();
        if(strlen($_POST['fields_for_sorting'])>0)
        {
          foreach(explode(',',str_replace('form_fields_','',$_POST['fields_for_sorting'])) as $v)
          {
            $listing_order_fields[] = $v . '_asc';
          }
        }
        
        if(count($listing_order_fields)>0)
        {
          db_query("update app_reports set listing_order_fields='" . db_input(implode(',',$listing_order_fields)) . "' where id='" . db_input($_GET['reports_id']) . "'");
        }
        else
        {
          db_query("update app_reports set listing_order_fields='' where id='" . db_input($_GET['reports_id']) . "'");
        } 
      exit();
    break;
  case 'set_sorting_condition':
      if($_POST['condition']=='desc')
      {
        db_query("update app_reports set listing_order_fields=replace(listing_order_fields,'" . $_POST['field_id']. "_asc','" . $_POST['field_id']. "_desc') where id='" . db_input($_GET['reports_id']) . "'");
      }
      else
      {
        db_query("update app_reports set listing_order_fields=replace(listing_order_fields,'" . $_POST['field_id']. "_desc','" . $_POST['field_id']. "_asc') where id='" . db_input($_GET['reports_id']) . "'");
      }
      
      exit();
    break;
}