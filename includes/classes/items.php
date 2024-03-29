<?php

class items
{
  public static function get_choices_by_entity($entity_id,$parent_entity_id,$report_id=0)
  {
    
    $heading_field_id = fields::get_heading_id($parent_entity_id);
        
    $listing_sql_query = '';
    
    //add reports filters
    if($report_id>0)
    {
      $listing_sql_query = reports::add_filters_query($report_id,$listing_sql_query);
    }
    
    //add access query
    $listing_sql_query = items::add_access_query($parent_entity_id,$listing_sql_query);
    
    //add order query
    if($heading_field_id>0)
    {
      $listing_sql_query .= " order by e.parent_item_id, e.field_" . $heading_field_id; 
    }
    else
    {
      $listing_sql_query .= " order by e.parent_item_id, e.id";
    }
        
    //build query
    $listing_sql = "select e.* from app_entity_" . $parent_entity_id . " e where e.id>0 " . $listing_sql_query;        
    $items_query = db_query($listing_sql);
    
    $choices = array();
    
    while($item = db_fetch_array($items_query))
    {
      $path_info = items::get_path_info($parent_entity_id,$item['id']);
      
      //print_r($path_info);
      
      $parent_name = '';
      if(strlen($path_info['parent_name'])>0)
      {
        $parent_name = str_replace('<br>',' / ',$path_info['parent_name']) . ' / '; 
      }
                  
      $choices[$path_info['full_path'] . '/' . $entity_id] = $parent_name . ($heading_field_id>0 ? $item['field_' . $heading_field_id] : $item['id']); 
    }
    
    return $choices;
  }
  
  public static function get_heading_field($entity_id,$item_id)
  {
    $heading_field_id = fields::get_heading_id($entity_id);
    $item_info = db_find('app_entity_' . $entity_id,$item_id);
    
    return ($heading_field_id>0 ? $item_info['field_' . $heading_field_id] : $item_info['id']);
  }
  
  public static function get_breadcrumb($path_array)
  {
    $breadcrumb = array();
    $path = '';
    
    foreach($path_array as $v)
    {
      $vv = explode('-',$v);
      $entity_id = $vv[0];
      $item_id = (isset($vv[1]) ? $vv[1]:0); 
                  
      $entity_info = db_find('app_entities',$entity_id);
      $entity_cfg = entities::get_cfg($entity_id);      
      $heading_field_id = fields::get_heading_id($entity_id);            
      
      $entitiy_name = (strlen($entity_cfg['menu_title'])>0 ? $entity_cfg['menu_title'] : $entity_info['name']);
            
      $breadcrumb[] = array('url'=>url_for('items/items','path=' . $path . $entity_id),'title'=>$entitiy_name);
      
      if($item_id>0)
      {
        $item_info = db_find('app_entity_' . $entity_id,$item_id);
        $item_name = ($heading_field_id>0 ? $item_info['field_' . $heading_field_id] : $item_info['id']);
        
        $breadcrumb[] = array('url'=>url_for('items/info','path=' . $path . $entity_id . '-' . $item_id),'title'=>$item_name);
      }
          
      $path .= $entity_id . ($item_id>0 ? '-' . $item_id . '/':'');
    }
            
    return $breadcrumb;  
  }
  public static function render_breadcrumb($breadcrumb)
  {
    $html = '';
    foreach($breadcrumb as $v)
    {
     $html .= '
        <li>
          <a href="' . $v['url']. '">' . $v['title'] . '</a>
          <i class="fa fa-angle-right"></i>
        </li>
      ';
    }
    
    return $html;  
  }
  
  public static function build_menu()
  {
    global $current_path,$current_path_array, $app_user;
                
    $entity_id = 0;
    $path_to_item = array();
    foreach($current_path_array as $v)
    {
      $vv = explode('-',$v);
      
      $count = db_count('app_entities',$vv[0], 'parent_id');
            
      if($count>0 and isset($vv[1]))
      {
        $entity_id = $vv[0];
        $item_id = $vv[1];
        
        $path_to_item[] = $v;
      }
    }
            
    $menu = array();
    
    
    
    if($entity_id>0)
    {        
      $entity_cfg = entities::get_cfg($entity_id);
                
      $menu[] = array('title'=>(strlen($entity_cfg['window_heading'])>0 ? $entity_cfg['window_heading'] : TEXT_INFO),'url'=>url_for('items/info','path=' . implode('/',$path_to_item)));
      
      if($app_user['group_id']==0)
      {
        $entities_query = db_query("select e.* from app_entities e where parent_id='" . db_input($entity_id) . "' order by e.sort_order, e.name");
      }
      else
      {
        $entities_query = db_query("select e.* from app_entities e, app_entities_access ea where e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input($app_user['group_id']) . "' and e.parent_id = '" . db_input($entity_id) . "' order by e.sort_order, e.name");
      }
      
      
      while($entities = db_fetch_array($entities_query))
      {    
        $entity_cfg = entities::get_cfg($entities['id']);
        
        $path = implode('/',$path_to_item) . '/' . $entities['id'];        
        
        $s = array();
        $s[] = array('title'=>TEXT_VIEW_ALL,'url'=>url_for('items/items','path=' . $path));
                
        if(users::has_access('create',users::get_entities_access_schema($entities['id'],$app_user['group_id'])))
        {
          $s[] = array('title'=>TEXT_ADD,'url'=>url_for('items/form','path=' . $path),'modalbox'=>true);
        }        
        $menu[] = array('title'=>(strlen($entity_cfg['menu_title'])>0 ? $entity_cfg['menu_title'] : $entities['name']),'url'=>url_for('items/items','path=' . $path), 'submenu'=>$s);
      }
      
      $s = array();
      
      if(count($plugin_menu = plugins::include_menu('items_menu_reports'))>0)
      {
        $s = array_merge($s,$plugin_menu);
      }
      
      if(count($s)>0)
      {
        $menu[] = array('title'=>TEXT_REPORTS,'submenu'=>$s);
      }
    }
    
    return $menu;
  }
  

  
  public static function render_info_box($entity_id,$item_id,$users_id=false)
  {
    global $current_path, $app_user,$app_users_cache;
    
    if($users_id>0)
    {
      $user_info = db_find('app_entity_1',$users_id);
      $fields_access_schema = users::get_fields_access_schema($entity_id,$user_info['field_6']);      
    }
    else
    { 
      $fields_access_schema = users::get_fields_access_schema($entity_id,$app_user['group_id']);
    }
    
    
    $choices_cache = fields_choices::get_cache();
    
    $item = db_find('app_entity_' . $entity_id,$item_id);
    
    $html = '';
    
    
    /**
     * display related records fields
     */         
    
    $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_related_records') and f.entities_id='" . db_input($entity_id) . "' and f.forms_tabs_id=t.id  order by t.sort_order, t.name, f.sort_order, f.name");
    while($field = db_fetch_array($fields_query))
    {            
      //check field access
      if(isset($fields_access_schema[$field['id']]))
      {
        if($fields_access_schema[$field['id']]=='hide') continue;
      }
      
      $cfg = fields_types::parse_configuration($field['configuration']);
      
      $access_schema = users::get_entities_access_schema($cfg['entity_id'],$app_user['group_id']);
      
      //checking view access
      if(!users::has_access('view',$access_schema) and  !users::has_access('view_assigned',$access_schema))
      {
        continue;
      }
      
    $output_options = array('class'=>$field['type'],
                            'value'=>$value,
                            'field'=>$field,
                            'item'=>$item,
                            'users_cache' =>$app_users_cache,
                            'display_user_photo'=>true,
                            'choices_cache'=>$choices_cache,
                            'path'=>$current_path);
      
        $html .='
         <div class="related-records-info-box">
           <div class="heading"><h4 class="media-heading">' . fields_types::get_option($field['type'],'name',$field['name']) . '</h4></div>  
           <div>' . fields_types::output($output_options) . '</div>
         </div>
        ';      
    }
    
    
    /**
     * display entity fields
     */
                
    
    $count = 0;
    $tabs_query = db_fetch_all('app_forms_tabs',"entities_id='" . db_input($entity_id) . "' order by  sort_order, name");
    while($tabs = db_fetch_array($tabs_query))
    {
              
      $html .= '
      <div class="heading"><h4 class="media-heading">' . $tabs['name']. '</h4></div>
      <div class="table-scrollable">
      <table class="table table-bordered table-hover table-item-details">
      ';
      
      $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in ('fieldtype_action','fieldtype_textarea','fieldtype_textarea_wysiwyg','fieldtype_attachments','fieldtype_related_records') and (f.is_heading is null or f.is_heading=0) and f.entities_id='" . db_input($entity_id) . "' and f.forms_tabs_id=t.id and f.forms_tabs_id='" . db_input($tabs['id']) . "' order by t.sort_order, t.name, f.sort_order, f.name");
      while($field = db_fetch_array($fields_query))
      {            
        //check field access
        if(isset($fields_access_schema[$field['id']]))
        {
          if($fields_access_schema[$field['id']]=='hide') continue;
        }
                
        switch($field['type'])
        {
          case 'fieldtype_created_by':
              $value = $item['created_by'];
            break;
          case 'fieldtype_date_added':
              $value = $item['date_added'];                
            break;
          case 'fieldtype_action':                
          case 'fieldtype_id':
              $value = $item['id'];
            break;
          default:
              $value = $item['field_' . $field['id']]; 
            break;
        }
        
        $output_options = array('class'=>$field['type'],
                            'value'=>$value,
                            'field'=>$field,
                            'item'=>$item,
                            'users_cache' =>$app_users_cache,
                            'display_user_photo'=>true,
                            'choices_cache'=>$choices_cache,
                            'path'=>$current_path);
      
        $html .='
          <tr>            
            <th>' . fields_types::get_option($field['type'],'name',$field['name']) . '</th>
            <td>' . fields_types::output($output_options). '</td>
          </tr>
        ';
      }
      
      $html .= '</table></div>';
      
      $count++;
    }
    
    return $html;
  }
  
  public static function render_content_box($entity_id,$item_id,$users_id = false)
  {
    global $current_path,$app_user;
    
    if($users_id>0)
    {
      $user_info = db_find('app_entity_1',$users_id);
      $fields_access_schema = users::get_fields_access_schema($entity_id,$user_info['field_6']);      
    }
    else
    {
      $fields_access_schema = users::get_fields_access_schema($entity_id,$app_user['group_id']);
    }
    
            
    $item = db_find('app_entity_' . $entity_id,$item_id);
    
    $html = '';
    $count = 0;

    $html = '';  
    $fields_query = db_query("select f.* from app_fields f where f.type in ('fieldtype_textarea','fieldtype_textarea_wysiwyg','fieldtype_attachments') and  f.entities_id='" . db_input($entity_id) . "' order by f.sort_order, f.name");
    while($field = db_fetch_array($fields_query))
    {   
      //check field access
      if(isset($fields_access_schema[$field['id']]))
      {
        if($fields_access_schema[$field['id']]=='hide') continue;
      }
            
      $value = $item['field_' . $field['id']];
      
      if($field['type']=='fieldtype_attachments' and strlen($value)==0) continue;
      
                       
      $output_options = array('class'=>$field['type'],
                            'value'=>$value,
                            'field'=>$field,
                            'item'=>$item,                            
                            'path'=>$current_path);
                                                                                    
      $value = fields_types::output($output_options);
    
      if(strlen($value)>0)
      {
        $html .='
          <div class="content_box_heading"><h4 class="media-heading">' . $field['name'] . '</h4></div>
          <div class="content_box_content">' . $value . '</div>
        ';
      }
    }
        

    
    return $html;
  }  
  
  public static function get_fields_values_cache($fields_cache,$path_array,$current_entity_id)
  {
    foreach($path_array as $v)
    {
      $vv = explode('-',$v);
      $entity_id = $vv[0];
      $item_id = (isset($vv[1]) ? $vv[1] : 0);             
      
      if($item_id==0 or $current_entity_id==$entity_id) break;
            
      $item_info = db_find('app_entity_' . $entity_id,$item_id);
      
      $fields_query = db_query("select f.* from app_fields f where f.type not in (" . fields_types::get_reserverd_types_list(). ") and  f.entities_id='" . db_input($entity_id) . "' order by f.sort_order, f.name");
      while($field = db_fetch_array($fields_query))
      {
        $fields_cache[$field['id']] = $item_info['field_' . $field['id']];
      }
           
    }
    
    return $fields_cache;
  }
  
  public static function get_path_info($entities_id, $items_id)
  {
    $path_array = items::get_path_array($entities_id,$items_id);
    
    
    $path_array = array_reverse($path_array);
    
    $cout = 0;
    $paent_path_list = array();
    $path_list = array();
    $name_list = array();
    $path_to_entity = array();
    foreach($path_array as $v)
    {
      $path_list[] = $v['path'];
      
      
      if($cout!=(count($path_array)-1))
      { 
        $paent_path_list[] = $v['path'];
        $name_list[] = $v['name'];
      }
      
      if($cout==(count($path_array)-1))
      {
        $last = explode('-',$v['path']);
        $path_to_entity[] = $last[0];
      }
      else
      {
        $path_to_entity[] = $v['path'];
      }
      
      $cout++;
    }
    
    return array('parent_name'=>implode('<br>',$name_list),
                 'parent_path'=>implode('/',$paent_path_list),
                 'full_path'=>implode('/',$path_list),
                 'path_to_entity'=> implode('/',$path_to_entity),
                );
    
    //print_r($path_array);
  }
  
  public static function get_path_array($entities_id, $items_id,$path_array = array())
  {
    $entities_query = db_query("select * from app_entities where id='" . $entities_id . "'");
    $entities = db_fetch_array($entities_query);
    
    $items_query = db_query("select * from app_entity_" . $entities_id . " where id='" . $items_id . "'");
    $items = db_fetch_array($items_query);
        
    
    $path_array[] = array('path'=>$entities_id . '-' . $items_id,'name'=>$items['field_' . fields::get_heading_id($entities_id)]);
            
    if($entities['parent_id']>0)
    {                         
      $path_array = items::get_path_array($entities['parent_id'],$items['parent_item_id'],$path_array);
    }
    
    return $path_array;
  }
  
  public static function parse_path($path)
  {
    $path_array = explode('/',$path);
    $item_array = explode('-',$path_array[count($path_array)-1]);
    
    $entity_id = $item_array[0];
    $item_id = (isset($item_array[1]) ? $item_array[1] : 0);
    
    if(count($path_array)>1)
    {
      $v = explode('-',$path_array[count($path_array)-2]);
      $parent_entity_item_id = $v[1];
    }
    else
    {
      $parent_entity_item_id = 0;
    }
    
    return array('entity_id' => $entity_id,
                 'item_id' => $item_id,
                 'path_array' => $path_array,
                 'parent_entity_item_id' => $parent_entity_item_id);
  }
  
  public static function get_paretn_entity_id_by_path($path)
  {
    $entity_id = 0;
    $path_array = explode('/',$path);
    foreach($path_array as $v)
    {
      $vv = explode('-',$v);
      
      $count = db_count('app_entities',$vv[0], 'parent_id');
            
      if($count>0 and isset($vv[1]))
      {
        $entity_id = $vv[0];                        
      }
    }
    
    return $entity_id;
  }
  
  public static function get_paretn_entity_item_id_by_path($path)
  {
    $item_id = 0;
    $path_array = explode('/',$path);
    foreach($path_array as $v)
    {
      $vv = explode('-',$v);
      
      $count = db_count('app_entities',$vv[0], 'parent_id');
            
      if($count>0 and isset($vv[1]))
      {
        $item_id = $vv[1];                        
      }
    }
    
    return $item_id;
  }
  
  public static function get_sub_entities_list_by_path($path)
  {   
    global $app_user;
    
    $parent_id = items::get_paretn_entity_id_by_path($path);
    
    $list = array();
    
    if($parent_id>0)
    {
      if($app_user['group_id']==0)
      {
        $entities_query = db_query("select e.* from app_entities e where parent_id='" . db_input($parent_id) . "' order by e.sort_order, e.name");
      }
      else
      {
        $entities_query = db_query("select e.* from app_entities e, app_entities_access ea where e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input($app_user['group_id']) . "' and e.parent_id = '" . db_input($parent_id) . "' order by e.sort_order, e.name");
      }
      
      
      while($entities = db_fetch_array($entities_query))
      {
        $list[] = $entities['id'];      
      }
    }
    
    return $list;
  }
  
  public static function add_access_query_for_parent_entities($entities_id,$listing_sql_query='')
  {
    global $app_user;
    
    if($app_user['group_id']==0) return '';
    
    $entity_info = db_find('app_entities',$entities_id);
            
    if($entity_info['parent_id']>0)
    {      
      $listing_sql_query = ' and e.parent_item_id in (select e.id from app_entity_' . $entity_info['parent_id']. ' e where e.id>0 '  . items::add_access_query($entity_info['parent_id'],'') . ' ' . items::add_access_query_for_parent_entities($entity_info['parent_id']) . ')'; 	
    } 
    
    return $listing_sql_query;
  }
  
  public static function add_access_query($current_entity_id,$listing_sql_query)
  {
    global $app_user;
    
    $access_schema = users::get_entities_access_schema($current_entity_id,$app_user['group_id']);
          
    if(users::has_access('view_assigned',$access_schema) and $app_user['group_id']>0)
    {                    
      $users_fields = array(); 
      $fields_query = db_query("select f.* from app_fields f where f.type in ('fieldtype_users') and  f.entities_id='" . db_input($current_entity_id) . "'");
      while($fields = db_fetch_array($fields_query))
      {    
        $users_fields[] = $fields['id'];
      }
      
      $grouped_users_fields = array(); 
      $fields_query = db_query("select f.* from app_fields f where f.type in ('fieldtype_grouped_users') and  f.entities_id='" . db_input($current_entity_id) . "'");
      while($fields = db_fetch_array($fields_query))
      {    
        $grouped_users_fields[] = $fields['id'];
      }
      
      
      if(count($users_fields)>0 or count($grouped_users_fields)>0)
      {
        $sql_query_array = array();
        foreach($users_fields as $id)
        {
          $sql_query_array[] = "find_in_set('" . $app_user['id']. "', e.field_" . $id. ")";
        }
        
        foreach($grouped_users_fields as $id)
        {
          $sql_query_array[] = "find_in_set('" . $app_user['id']. "', SUBSTRING(e.field_" . $id. ",locate('|',e.field_" . $id . ")+1))";
        }
        
        $sql_query_array[] = "e.created_by='" . $app_user['id'] . "'";
        
        $listing_sql_query .= " and (" . implode(' or ', $sql_query_array). ") ";
      }
    }
    
    return $listing_sql_query;     
  
  }
  
  public static function auto_calculate_count_related_items($entities_id,$items_id,$related_entities_id,$related_items_id)
  {
    $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_related_records') and f.entities_id='" . db_input($entities_id) . "' and f.forms_tabs_id=t.id  order by t.sort_order, t.name, f.sort_order, f.name");
    while($field = db_fetch_array($fields_query))
    {
      $cfg = fields_types::parse_configuration($field['configuration']);
      
      if($cfg['entity_id']==$related_entities_id)
      {
        $count_query = db_query("select count(*) as total from app_related_items where (entities_id = '"  . db_input($entities_id) . "' and items_id = '" . db_input($items_id) . "' and related_entities_id='" . db_input($cfg['entity_id']) . "') or (related_entities_id = '"  . db_input($entities_id) . "' and related_items_id = '" . db_input($items_id) . "' and entities_id='" . db_input($cfg['entity_id']) . "')");
        $count = db_fetch_array($count_query);
        
        db_query("update app_entity_" . $entities_id . " set field_" . $field['id'] . "='" . $count['total']. "' where id='" . $items_id . "'");
      }
    }
    
    
    $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_related_records') and f.entities_id='" . db_input($related_entities_id) . "' and f.forms_tabs_id=t.id  order by t.sort_order, t.name, f.sort_order, f.name");
    while($field = db_fetch_array($fields_query))
    {
      $cfg = fields_types::parse_configuration($field['configuration']);
      
      if($cfg['entity_id']==$entities_id)
      {
        $count_query = db_query("select count(*) as total from app_related_items where (related_entities_id = '"  . db_input($related_entities_id) . "' and related_items_id = '" . db_input($related_items_id) . "' and entities_id='" . db_input($cfg['entity_id']) . "') or (entities_id = '"  . db_input($related_entities_id) . "' and items_id = '" . db_input($related_items_id) . "' and related_entities_id='" . db_input($cfg['entity_id']) . "')");
        $count = db_fetch_array($count_query);
        
        db_query("update app_entity_" . $related_entities_id . " set field_" . $field['id'] . "='" . $count['total']. "' where id='" . $related_items_id . "'");
      }
    }
        
  }
  
  
}