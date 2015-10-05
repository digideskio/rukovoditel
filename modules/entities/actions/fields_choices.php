<?php

switch($app_module_action)
{
  case 'save':
      $sql_data = array('fields_id'=>$_POST['fields_id'],
                        'parent_id'=>(strlen($_POST['parent_id'])==0 ? 0 : $_POST['parent_id']),
                        'name'=>$_POST['name'],                        
                        'users'=> (isset($_POST['users']) ? implode(',',$_POST['users']):''),
                        'is_default'=>(isset($_POST['is_default']) ? $_POST['is_default']:0),
                        'bg_color'=>$_POST['bg_color'],                        
                        'sort_order'=>$_POST['sort_order'],
                        );
                                                                              
      if(isset($_POST['is_default']))
      {
        db_query("update app_fields_choices set is_default = 0 where fields_id = '" . db_input($_POST['fields_id']). "'");
      }                        
      
      if(isset($_GET['id']))
      {        
        db_perform('app_fields_choices',$sql_data,'update',"id='" . db_input($_GET['id']) . "'");       
      }
      else
      {               
        db_perform('app_fields_choices',$sql_data);
      }
      
      redirect_to('entities/fields_choices','entities_id=' . $_POST['entities_id']. '&fields_id=' . $_POST['fields_id']);      
    break;
  case 'delete':
      if(isset($_GET['id']))
      {      
        $msg = fields_choices::check_before_delete($_GET['id']);
        
        if(strlen($msg)>0)
        {
          $alerts->add($msg,'error');
        }
        else
        {
          $name = fields_choices::get_name_by_id($_GET['id']);
          
          $tree = fields_choices::get_tree($_GET['fields_id'],$_GET['id']);
          
          foreach($tree as $v)
          {
            db_delete_row('app_fields_choices',$v['id']);
          }
          
          db_delete_row('app_fields_choices',$_GET['id']);
          
          $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS,$name),'success');
        }
        
        redirect_to('entities/fields_choices','entities_id=' . $_GET['entities_id'] . '&fields_id=' . $_GET['fields_id']);  
      }
    break;    
}

$field_info = db_find('app_fields',$_GET['fields_id']);