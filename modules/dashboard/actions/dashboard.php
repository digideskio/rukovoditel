<?php

$app_title = app_set_title(TEXT_MENU_DASHBOARD);

switch($app_module_action)
{
  case 'keep_session':
      exit();
    break; 
  case 'sort_reports':
  
        if(isset($_POST['reports_on_dashboard'])) 
        {
          $sort_order = 0;
          foreach(explode(',',$_POST['reports_on_dashboard']) as $v)
          {
            $sql_data = array('in_dashboard'=>1,'dashboard_sort_order'=>$sort_order);
            db_perform('app_reports',$sql_data,'update',"id='" . db_input(str_replace('report_','',$v)) . "' and created_by='" . db_input($app_user['id']). "'");
            $sort_order++;
          }
        }
        
        if(isset($_POST['reports_excluded_from_dashboard'])) 
        {          
          foreach(explode(',',$_POST['reports_excluded_from_dashboard']) as $v)
          {
            $sql_data = array('in_dashboard'=>0,'dashboard_sort_order'=>0);
            db_perform('app_reports',$sql_data,'update',"id='" . db_input(str_replace('report_','',$v)) . "' and created_by='" . db_input($app_user['id']). "'");            
          }
        }
        
      exit();
    break;   
} 