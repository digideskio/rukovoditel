<?php

$current_reports_info_query = db_query("select * from app_reports where id='" . db_input($_GET['reports_id']). "'");
if(!$current_reports_info = db_fetch_array($current_reports_info_query))
{
  $alerts->add(TEXT_REPORT_NOT_FOUND,'error');
  redirect_to('reports/');
}

switch($app_module_action)
{
  case 'save':
    
    $values = '';
    
    if(isset($_POST['values']))
    {
      if(is_array($_POST['values']))
      {
        $values = implode(',',$_POST['values']);
      }
      else
      {
        $values = $_POST['values'];
      }
    }
    $sql_data = array('reports_id'=>(isset($_GET['parent_reports_id']) ? $_GET['parent_reports_id']:$_GET['reports_id']),
                      'fields_id'=>$_POST['fields_id'],
                      'filters_condition'=>$_POST['filters_condition'],                                              
                      'filters_values'=>$values,
                      );
        
    if(isset($_GET['id']))
    {        
      db_perform('app_reports_filters',$sql_data,'update',"id='" . db_input($_GET['id']) . "'");       
    }
    else
    {               
      db_perform('app_reports_filters',$sql_data);                  
    }
    
    plugins::handle_action('filters_redirect');
    
    switch($app_redirect_to)
    {
      case 'listing':
          redirect_to('items/items','path=' . $_POST['path']);
        break;
      case 'report':
          redirect_to('reports/view','reports_id=' . $_GET['reports_id']);
        break;
      default:
          redirect_to('reports/filters','reports_id=' . $_GET['reports_id']);
        break;
    }
        
          
  break;
  case 'delete':
      if(isset($_GET['id']))
      {      
        if($_GET['id']=='all')
        {
          db_query("delete from app_reports_filters where reports_id='" . db_input((isset($_GET['parent_reports_id']) ? $_GET['parent_reports_id']:$_GET['reports_id'])) . "'");
          $alerts->add(TEXT_WARN_DELETE_ALL_FILTERS_SUCCESS,'success');
        }
        else
        {
          db_query("delete from app_reports_filters where id='" . db_input($_GET['id']) . "'");          
          //$alerts->add(TEXT_WARN_DELETE_FILTER_SUCCESS,'success');
        }
        
        plugins::handle_action('filters_redirect');
                                                      
        switch($app_redirect_to)
        {
          case 'listing':
              redirect_to('items/items','path=' . $_GET['path']);
            break;
          case 'report':
              redirect_to('reports/view','reports_id=' . $_GET['reports_id']);
            break;
          default:
              redirect_to('reports/filters','reports_id=' . $_GET['reports_id']);
            break;
        }  
      }
    break;   
}