<?php

  if (!app_session_is_registered('import_fields')) 
  {
    $import_fields = array();
    app_session_register('import_fields');    
  } 

  switch($app_module_action)
  {   
    case 'import':
        $worksheet = json_decode(stripslashes($_POST['worksheet']),true);
        $entities_id = $_POST['entities_id'];
        $parent_item_id = $_POST['parent_item_id'];
                        
        if(count($import_fields)>0)
        {
          $first_row  = (isset($_POST['import_first_row']) ? 0:1); 
          
          for ($row = $first_row; $row < count($worksheet); ++$row) 
          {                                    
            for ($col = 0; $col <= count($worksheet[$row]); ++$col) 
            {
              if(isset($import_fields[$col]) and strlen($worksheet[$row][$col])>0)
              {
                $field_id = $import_fields[$col]; 
                $filed_info_query = db_query("select * from app_fields where id='" . db_input($field_id). "'");
                if($filed_info = db_fetch_array($filed_info_query))
                {
                  switch($filed_info['type'])
                  { 
                    case 'fieldtype_dropdown':
                    case 'fieldtype_radioboxes':
                        $value = trim($worksheet[$row][$col]);                                                
                        $fields_choices_info_query = db_query("select * from app_fields_choices where name='" . db_input($value) . "' and fields_id='" . db_input($field_id) . "'");
                        if($fields_choices_info = db_fetch_array($fields_choices_info_query))
                        {
                          $sql_data['field_' . $field_id] = $fields_choices_info['id'];
                        }
                        else
                        {
                          $field_sql_data = array('fields_id'=>$field_id,
                                            'parent_id'=>0,
                                            'name'=>$value);
                          db_perform('app_fields_choices',$field_sql_data);
                          
                          $item_id = db_insert_id();
                          
                          $sql_data['field_' . $field_id] = $item_id;
                        }
                        
                      break;
                    case 'fieldtype_checkboxes':
                        $values_list = array();
                        $value = trim($worksheet[$row][$col]);
                        foreach(explode(',',$value) as $value_name)
                        {
                          $fields_choices_info_query = db_query("select * from app_fields_choices where name='" . db_input(trim($value_name)) . "' and fields_id='" . db_input($field_id) . "'");
                          if($fields_choices_info = db_fetch_array($fields_choices_info_query))
                          {
                            $values_list[] = $fields_choices_info['id'];
                          }
                          else
                          {
                            $field_sql_data = array('fields_id'=>$field_id,
                                                    'parent_id'=>0,
                                                    'name'=>trim($value_name));
                            db_perform('app_fields_choices',$field_sql_data);
                            
                            $item_id = db_insert_id();
                            
                            $values_list[] = $item_id;                            
                          }                        
                        }
                        
                        $sql_data['field_' . $field_id] = implode(',',$values_list);
                                            
                      break;  
                    case 'fieldtype_input_date':
                    case 'fieldtype_input_datetime':
                        $sql_data['field_' . $field_id] = strtotime($worksheet[$row][$col]);
                      break;
                    default:               
                        $sql_data['field_' . $field_id] = $worksheet[$row][$col];
                      break;                  
                  }
                }
              }            
            }                      
            
            $sql_data['date_added'] = time();              
            $sql_data['created_by'] = $app_logged_users_id;
            $sql_data['parent_item_id'] = (int)$parent_item_id;
            
            //echo '<pre>';
            //print_r($sql_data);
            
            db_perform('app_entity_' . $entities_id,$sql_data);          
          }
        }
        
        //exit();
                                
        $entity_info = db_find('app_entities',$entities_id);
                         
        if($entity_info['parent_id']>0)
        {                  
          $parent_item_query = db_query("select * from app_entity_" . $entity_info['parent_id'] . " where id='" . db_input($parent_item_id) . "'");
          
          if($parent_item = db_fetch_array($parent_item_query))
          {
            $path_info = items::get_path_info($entity_info['parent_id'],$parent_item['id']);
            
            redirect_to('items/items','path=' . $path_info['full_path'] . '/' . $entities_id); 
          }
        }
        else
        {
          redirect_to('items/items','path=' . $entities_id);
        }        
        
        exit();
      break; 
    case 'bind_field':
        $col = $_POST['col'];
        $filed_id = $_POST['filed_id'];
        
        if($filed_id>0)
        {
          $import_fields[$col] = $filed_id;
          
          $field_info = db_find('app_fields',$filed_id);
          
          echo $field_info['name'];
        }
        elseif(isset($import_fields[$col]))
        {
          unset($import_fields[$col]);
          echo '';
        } 
        
        exit();
      break;
    case 'set_parent_item_id':
        $entity_id = $_POST['entity_id'];
        
        $entity_info = db_find('app_entities',$entity_id);
        
        if($entity_info['parent_id']>0)
        {
          $parent_entity_info = db_find('app_entities',$entity_info['parent_id']);
          
          echo '
           <div class="form-group">
          	<label class="col-md-3 control-label" for="name">' . TEXT_PARENT_ITEM_ID . '</label>
            <div class="col-md-9">	
          	  ' .  input_tag('parent_item_id','',array('class'=>'form-control input-small required number'))  . input_hidden_tag('paren_entity_id',$entity_info['parent_id']). '
              <span class="help-block">' . sprintf(TEXT_PARENT_ITEM_ID_INFO,$parent_entity_info['name']) . '</span>
            </div>			
          </div>
          ';
        }
        
        exit();
      break;
  }