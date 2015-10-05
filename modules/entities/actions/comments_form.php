<?php
switch($app_module_action)
{
  case 'set_fields':
        if(isset($_POST['fields_in_comments'])) 
        {
          $sort_order = 0;
          foreach(explode(',',$_POST['fields_in_comments']) as $v)
          {
            $sql_data = array('comments_status'=>1,'comments_sort_order'=>$sort_order);
            db_perform('app_fields',$sql_data,'update',"id='" . db_input(str_replace('fields_','',$v)) . "'");
            $sort_order++;
          }
        }
        
        if(isset($_POST['available_fields'])) 
        {          
          foreach(explode(',',$_POST['available_fields']) as $v)
          {
            $sql_data = array('comments_status'=>0,'comments_sort_order'=>0);
            db_perform('app_fields',$sql_data,'update',"id='" . db_input(str_replace('fields_','',$v)) . "'");            
          }
        }
      exit();
    break;
}