<?php

$app_title = app_set_title(TEXT_HEADING_REPORTS);

switch($app_module_action)
{
  case 'save':
    $sql_data = array('name'=>$_POST['name'],
                      'entities_id'=>$_POST['entities_id'],
                      'reports_type'=>'standard',                                              
                      'in_menu'=>(isset($_POST['in_menu']) ? $_POST['in_menu']:0),
                      'in_dashboard'=>(isset($_POST['in_dashboard']) ? $_POST['in_dashboard']:0),
                      'created_by'=>$app_logged_users_id,
                      );
        
    if(isset($_GET['id']))
    {        
      
      $report_info = db_find('app_reports',$_GET['id']);
      
      //check reprot entity and if it's changed remove report filters
      if($report_info['entities_id']!=$_POST['entities_id'])
      {
        db_query("delete from app_reports_filters where reports_id='" . db_input($_GET['id']) . "'");
      }
      
      db_perform('app_reports',$sql_data,'update',"id='" . db_input($_GET['id']) . "' and created_by='" . $app_logged_users_id . "'");       
    }
    else
    {                     
      db_perform('app_reports',$sql_data);   
      
      $insert_id = db_insert_id();
      
      reports::auto_create_parent_reports($insert_id);               
    }
        
    redirect_to('reports/');      
  break;
  case 'delete':
      if(isset($_GET['id']))
      {      
      
        //delete paretn reports
        $paretn_reports = reports::get_parent_reports($_GET['id']);
        
        if(count($paretn_reports)>0)
        {
          foreach($paretn_reports as $id)
          {
            db_query("delete from app_reports where id='" . db_input($id) . "' and created_by='" . db_input($app_logged_users_id) . "'");
            db_query("delete from app_reports_filters where reports_id='" . db_input($id) . "'");
          }
        }

        db_query("delete from app_reports where id='" . db_input($_GET['id']) . "' and created_by='" . db_input($app_logged_users_id) . "'");
        db_query("delete from app_reports_filters where reports_id='" . db_input($_GET['id']) . "'");
                            
        $alerts->add(TEXT_WARN_DELETE_REPORT_SUCCESS,'success');
     
                
        redirect_to('reports/');  
      }
    break;   
}