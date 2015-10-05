<?php

switch($app_module_action)
{  
  case 'save':
  
      //checking access
      if(isset($_GET['id']) and !users::has_access('update'))
      {        
        redirect_to('dashboard/access_forbidden');
      }
      elseif(!users::has_access('create') and !isset($_GET['id']))
      {
        redirect_to('dashboard/access_forbidden');
      }
      
      //check POST data for user form
      if($current_entity_id==1)
      {      
        require(component_path('items/validate_users_form'));
      }
            
      $fields_values_cache = items::get_fields_values_cache($_POST['fields'],$current_path_array,$current_entity_id);      
      
      $fields_access_schema = users::get_fields_access_schema($current_entity_id,$app_user['group_id']);
      
      $app_send_to = array();
            
      $sql_data = array();
      
      //prepare item data                              
      $fields_query = db_query("select f.* from app_fields f where f.type not in (" . fields_types::get_reserverd_types_list(). ") and  f.entities_id='" . db_input($current_entity_id) . "' order by f.sort_order, f.name");
      while($field = db_fetch_array($fields_query))
      {
        $default_field_value = '';
        
        //check field access and skip fields without access
        if(isset($fields_access_schema[$field['id']]))
        { 
          //for new item check if there is default value and assign it if it's exist
          if(!isset($_GET['id']) and in_array($field['type'],fields_types::get_types_wich_choices()))
          {            
            $check_query = db_query("select id from app_fields_choices where fields_id='" . $field['id'] . "' and is_default=1");
            if($check = db_fetch_array($check_query))
            {
              $default_field_value = $check['id'];                            
            }
            else
            {
              continue;
            }
          }
          else
          {
            continue;
          }
        }                
        
        $value = (isset($_POST['fields'][$field['id']]) ? $_POST['fields'][$field['id']] : $default_field_value);
        
        $process_options = array('class'=>$field['type'],'value'=>$value,'fields_cache'=>$fields_values_cache, 'field'=>$field);
        
        $sql_data['field_' . $field['id']] = fields_types::process($process_options);
      } 
                        
      if(isset($_GET['id']))
      {                
        db_perform('app_entity_' . $current_entity_id,$sql_data,'update',"id='" . db_input($_GET['id']) . "'");
        $item_id = $_GET['id'];      
      }
      else
      { 
        //genreation user password and sending notification for new user
        if($current_entity_id==1)
        {      
          require(component_path('items/crete_new_user'));
        }
        
        $sql_data['date_added'] = time();              
        $sql_data['created_by'] = $app_logged_users_id;
        $sql_data['parent_item_id'] = $parent_entity_item_id;
        db_perform('app_entity_' . $current_entity_id,$sql_data);
        $item_id = db_insert_id();                
      }
      
      
                  
      //set email to users
      if(CFG_EMAIL_COPY_SENDER==1)
      {
        $app_send_to[] = $app_user['id'];
      }
      
      if(count($app_send_to)>0 and !isset($_GET['id']))
      {           
        $heading_field_id = fields::get_heading_id($current_entity_id);
        $item_name = ($heading_field_id>0 ? $sql_data['field_' . $heading_field_id] : $sql_data['id']);
        
        $cfg = entities::get_cfg($current_entity_id);
                   
        $subject = (strlen($cfg['email_subject_new_item'])>0 ? $cfg['email_subject_new_item'] . ' ' . $item_name : TEXT_DEFAULT_EMAIL_SUBJECT_NEW_ITEM . ' ' . $item_name);
        $heading = users::use_email_pattern_style('<div><a href="' . url_for('items/info','path=' . $_POST['path'] . '-' . $item_id,true) . '"><h3>' . $subject . '</h3></a></div>','email_heading_content');
          
        foreach(array_unique($app_send_to) as $send_to)
        {      
          $body = users::use_email_pattern('single',array('email_body_content'=>items::render_content_box($current_entity_id,$item_id,$send_to),'email_sidebar_content'=>items::render_info_box($current_entity_id,$item_id,$send_to)));
          users::send_to(array($send_to),$subject,$heading . $body);                                       
        }                
      }
      
      //redirect to related item
      if(isset($_POST['related']))
      {   
        $related_array = explode('-',$_POST['related']);
        $related_entities_id = $related_array[0];
        $related_items_id = $related_array[1]; 
           
        $sql_data = array('entities_id'=> $related_entities_id,
                          'items_id'=>$related_items_id,
                          'related_entities_id'=> $current_entity_id,
                          'related_items_id'=> $item_id);
                          
        db_perform('app_related_items',$sql_data);
        
        items::auto_calculate_count_related_items($related_entities_id,$related_items_id,$current_entity_id,$item_id);
                  
        if(count($current_path_array)>1)
        {     
          $redirect_path = app_get_path_to_parent_item($current_path_array) . '/' . $_POST['related'];
        }
        else
        {
          $redirect_path = $_POST['related'];
        }
        
        redirect_to('items/info','path=' . $redirect_path); 
      }
            
      //redirect to sub entity                  
      if(!isset($_GET['id']) and $app_redirect_to=='')
      {
        $entity_query = db_query("select * from app_entities where parent_id='" . db_input($current_entity_id) . "' order by sort_order, name limit 1");
        if($entity = db_fetch_array($entity_query))
        {
          redirect_to('items/items','path=' . $_POST['path'] . '-' . $item_id . '/' . $entity['id']);
        }
      }
      
      //other redirects      
      switch($app_redirect_to)
      {
        case 'dashboard':
            redirect_to('dashboard/');
          break;
        case 'items_info':
            redirect_to('items/info','path=' . $_POST['path']);
          break;
        default:
            if(strstr($app_redirect_to,'report_'))
            {
              redirect_to('reports/view','reports_id=' . str_replace('report_','',$app_redirect_to));
            }                                      
            else
            {              
              redirect_to('items/items','path=' . $_POST['path']);
            }  
          break;
      }
      
      
    break;  
  case 'delete':
      if(!users::has_access('delete'))
      {
        redirect_to('dashboard/access_forbidden');
      }
      
      $path_info = items::get_path_info($current_entity_id,$_GET['id']);
                  
      attachments::delete_attachments($current_entity_id,$_GET['id']);
      
      db_delete_row('app_entity_' . $current_entity_id,$_GET['id']);
      
      comments::delete_item_comments($current_entity_id,$_GET['id']);
      
      plugins::handle_action('delete_item');
            
      switch($app_redirect_to)
      {
        case 'dashboard':
            redirect_to('dashboard/');
          break;
        default:
        
            if(strstr($app_redirect_to,'report_'))
            {
              redirect_to('reports/view','reports_id=' . str_replace('report_','',$app_redirect_to));
            }                                      
            else
            {              
              redirect_to('items/items','path=' . $path_info['path_to_entity']);
            }  
            
          break;
      }
      
      
    break;
  case 'attachments_upload':    
        $verifyToken = md5('unique_salt' . $_POST['timestamp']);
          
        if(strlen($_FILES['Filedata']['tmp_name']) and $_POST['token'] == $verifyToken)
        {
          $file = attachments::prepare_filename($_FILES['Filedata']['name']);
                                        
          if(move_uploaded_file($_FILES['Filedata']['tmp_name'], DIR_WS_ATTACHMENTS  . $file['folder']  .'/'. $file['file']))
          {  
            //add attachments to tmp table
            $sql_data = array('form_token'=>$verifyToken,'filename'=>$file['name'],'date_added'=>date('Y-m-d'));
            db_perform('app_attachments',$sql_data);                                                
            
          }
        }
      exit();
    break;
    
  case 'attachments_preview': 
        $field_id = $_GET['field_id'];  
        
        $attachments_list = $uploadify_attachments[$field_id];
        
        //get new attachments
        $attachments_query = db_query("select filename from app_attachments where form_token='" . db_input($_GET['token']). "'");
        while($attachments = db_fetch_array($attachments_query))
        {
          $attachments_list[] = $attachments['filename']; 
        }
                     
        echo attachments::render_preview($field_id, $attachments_list);
        
      exit();
    break;  
  case 'remove_related_item':
      $related_item_query = db_query("select * from app_related_items where id='" . db_input($_POST['id']). "'");
      if($related_item = db_fetch_array($related_item_query))
      {
        db_delete_row('app_related_items', $_POST['id']);
        items::auto_calculate_count_related_items($related_item['entities_id'],$related_item['items_id'],$related_item['related_entities_id'],$related_item['related_items_id']);
      }
    break;
  case 'search_item_by_id':
        $items_query = db_query("select * from app_entity_" . $_POST['related_entities_id'] . " where id='" . $_POST['id']. "'");
        if($items = db_fetch_array($items_query))
        {
          $access_schema = users::get_entities_access_schema($_POST['related_entities_id'],$app_user['group_id']);
          
          //check assigned access
          if(users::has_access('view_assigned',$access_schema) and $app_user['group_id']>0)
          {
            if(!users::has_access_to_assigned_item($_POST['related_entities_id'],$items['id']))
            {
              echo '<div class="alert alert-warning">' . TEXT_NO_RECORDS_FOUND . '</div>';
              exit(); 
            }
          }
          
          $name = items::get_heading_field($_POST['related_entities_id'],$items['id']);
          $path_info = items::get_path_info($_POST['related_entities_id'],$items['id']);
          
          echo '
            <div class="alert alert-info"><a href="' . url_for('items/info','path=' . $path_info['full_path']) . '" target="_blank">' . $name. '</a></div>
            <p>' . submit_tag(TEXT_BUTTON_LINK) . '</p>' . input_hidden_tag('related_items_id',$items['id']);
        }
        else
        {
          echo '<div class="alert alert-warning">' . TEXT_NO_RECORDS_FOUND . '</div>';
        }
      exit;
    break;
  case 'add_related_item':
        $related_entities_id = $_POST['related_entities_id'];
        $related_items_id = $_POST['related_items_id'];
        
        $check_query = db_query("select * from app_related_items where (entities_id ='" . $current_entity_id . "' and items_id ='" . $current_item_id . "' and related_entities_id = '" .$related_entities_id ."' and related_items_id = '" . $related_items_id . "') or (entities_id ='" . $related_entities_id . "' and items_id ='" . $related_items_id . "' and  related_entities_id = '" .$current_entity_id ."' and related_items_id = '" . $current_item_id . "')");
        if(!$check = db_fetch_array($check_query))
        {            
          $sql_data = array('entities_id'=> $current_entity_id,
                            'items_id'=>$current_item_id,
                            'related_entities_id'=> $related_entities_id,
                            'related_items_id'=> $related_items_id);
                            
          db_perform('app_related_items',$sql_data);
          
          items::auto_calculate_count_related_items($current_entity_id,$current_item_id,$related_entities_id,$related_items_id);
        }
        
        redirect_to('items/info','path=' . $_GET['path']);
      
    break;  
  
}

$entity_info = db_find('app_entities',$current_entity_id);
$entity_cfg = entities::get_cfg($current_entity_id);

$entity_listing_heading = (strlen($entity_cfg['listing_heading'])>0 ? $entity_cfg['listing_heading'] : $entity_info['name']);

$app_title = app_set_title($entity_listing_heading);

//create default entity report for logged user
$reports_info = reports::create_default_entity_report($current_entity_id, 'entity');
 

