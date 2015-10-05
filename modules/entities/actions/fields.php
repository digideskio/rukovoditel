<?php

switch($app_module_action)
{
  case 'sort_fields':
        if(isset($_POST['fields_in_listing'])) 
        {
          $sort_order = 0;
          foreach(explode(',',$_POST['fields_in_listing']) as $v)
          {
            $sql_data = array('listing_status'=>1,'listing_sort_order'=>$sort_order);
            db_perform('app_fields',$sql_data,'update',"id='" . db_input(str_replace('form_fields_','',$v)) . "'");
            $sort_order++;
          }
        }
        
        if(isset($_POST['fields_excluded_from_listing'])) 
        {          
          foreach(explode(',',$_POST['fields_excluded_from_listing']) as $v)
          {
            $sql_data = array('listing_status'=>0,'listing_sort_order'=>0);
            db_perform('app_fields',$sql_data,'update',"id='" . db_input(str_replace('form_fields_','',$v)) . "'");            
          }
        }
      exit();
    break;
  case 'save':
      $sql_data = array('forms_tabs_id'=>$_POST['forms_tabs_id'],
                        'name'=>$_POST['name'],                        
                        'type'=>$_POST['type'],
                        'short_name'=>$_POST['short_name'],
                        'is_heading'=>(isset($_POST['is_heading']) ? $_POST['is_heading']:0),
                        'is_required'=>(isset($_POST['is_required']) ? $_POST['is_required']:0),
                        'required_message'=>$_POST['required_message'],
                        'tooltip'=>$_POST['tooltip'],
                        'configuration'=> (isset($_POST['fields_configuration']) ? fields_types::prepare_configuration($_POST['fields_configuration']):''),        
                        'entities_id'=>$_POST['entities_id']);
      
      if(isset($_GET['id']))
      {        
        db_perform('app_fields',$sql_data,'update',"id='" . db_input($_GET['id']) . "'");       
      }
      else
      {     
        $sql_data['sort_order'] = (fields::get_last_sort_number($_POST['forms_tabs_id'])+1);
                  
        db_perform('app_fields',$sql_data);
        $fields_id = db_insert_id();
        
        entities::prepare_field($_POST['entities_id'],$fields_id);
        
        
        //autocalculate related items if field type fieldtype_related_records
        if($_POST['type']=='fieldtype_related_records')
        {
          $field_info = db_find("app_fields",$fields_id);
          $cfg = fields_types::parse_configuration($field_info['configuration']);
          
          $items_query = db_query("select * from app_entity_" . $_POST['entities_id']);
          while($items = db_fetch_array($items_query))
          {
            $count_query = db_query("select count(*) as total from app_related_items where related_entities_id = '"  . db_input($_POST['entities_id']) . "' and related_items_id = '" . db_input($items['id']) . "' and entities_id='" . db_input($cfg['entity_id']) . "'");
            $count = db_fetch_array($count_query);
        
            db_query("update app_entity_" . $_POST['entities_id'] . " set field_" . $fields_id . "='" . $count['total']. "' where id='" . $items['id'] . "'");
          }          
        }
        
      }
      
      if(isset($_POST['redirect_to']))
      {
        switch($_POST['redirect_to'])
        {
          case 'forms':
              redirect_to('entities/forms','entities_id=' . $_POST['entities_id']);
            break;
        }
      }
      
      redirect_to('entities/fields','entities_id=' . $_POST['entities_id']);      
    break;
  case 'delete':
      if(isset($_GET['id']))
      {      
        $msg = fields::check_before_delete($_GET['id']);
        
        if(strlen($msg)>0)
        {
          $alerts->add($msg,'error');
        }
        else
        {
          $name = fields::get_name_by_id($_GET['id']);
          
          db_delete_row('app_fields',$_GET['id']);
          
          db_delete_row('app_reports_filters',$_GET['id'],'fields_id');
                              
          entities::delete_field($_GET['entities_id'],$_GET['id']);
          
          $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS,$name),'success');
        }
        
        if(isset($_POST['redirect_to']))
        {
          switch($_POST['redirect_to'])
          {
            case 'forms':
                redirect_to('entities/forms','entities_id=' . $_GET['entities_id']);
              break;
          }
        }
        
        redirect_to('entities/fields','entities_id=' . $_GET['entities_id']);  
      }
    break;    
}


require(component_path('entities/check_entities_id'));  