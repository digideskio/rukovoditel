<?php

class fields
{

  public static function check_before_delete($id)
  {     
    return '';
  }
  
  public static function get_name_by_id($id)
  {
    $obj = db_find('app_fields',$id);
    
    return $obj['name'];
  }
  
  public static function get_name_cache()
  {
    $cache = array();
    $fields_query = db_query("select * from app_fields");
    while($fields = db_fetch_array($fields_query))
    {
      switch($fields['type'])
      {
        case 'fieldtype_date_added':
            $cache[$fields['id']] = TEXT_FIELDTYPE_DATEADDED_TITLE;
          break;
        default:
            $cache[$fields['id']] = $fields['name'];
          break;
      }
    }
    
    return $cache;
    
  }
  
  public static function get_heading_id($entity_id)
  {
    $info_query = db_query("select * from app_fields where entities_id='" . db_input($entity_id) . "' and is_heading=1");
    if($v = db_fetch_array($info_query))
    {
      return $v['id'];
    }
    else
    {
      return false;
    }
    
    
  }
  
  public static function get_last_sort_number($forms_tabls_id)
  {
    $v = db_fetch_array(db_query("select max(sort_order) as max_sort_order from app_fields where forms_tabs_id = '" . db_input($forms_tabls_id) . "'"));
    
    return $v['max_sort_order'];
  } 
  
  public static function render_required_messages($entities_id)
  {
    $html = '';
    
    $fields_query = db_query("select f.* from app_fields f where f.type not in (" . fields_types::get_reserverd_types_list(). ") and  f.entities_id='" . db_input($entities_id) . "' order by f.sort_order, f.name");
    while($v = db_fetch_array($fields_query))
    {
      if(strlen($v['required_message'])>0)
      {
        $html .='\'field_' . $v['id'] . '\': "' . htmlspecialchars($v['required_message']) . '",' . "\n";
      }
    }
    
    return $html;
  }
  
  public static function render_required_ckeditor_ruels($entities_id)
  {
    $html = '';
    
    $fields_query = db_query("select f.* from app_fields f where f.type = 'fieldtype_textarea_wysiwyg' and is_required=1 and  f.entities_id='" . db_input($entities_id) . "' order by f.sort_order, f.name");
    while($v = db_fetch_array($fields_query))
    {
        $html .='
          "fields[' . $v['id'] . ']": { 
            required: function(element){
              CKEDITOR_holders["fields_' . $v['id'] . '"].updateElement();             
            }
          },' . "\n";
    }
    
    return $html;
  }
  
  public static function get_search_feidls($entity_id)
  {
    global $fields_access_schema;
    
    $search_fields = array();
    $fields_query = db_query("select f.* from app_fields f where  f.entities_id='" . db_input($entity_id) . "' order by f.listing_sort_order, f.name");
    while($v = db_fetch_array($fields_query))
    {
      //check field access
      if(isset($fields_access_schema[$v['id']]))
      { 
        if($fields_access_schema[$v['id']]=='hide') continue;
      }
      
      $cfg = fields_types::parse_configuration($v['configuration']);      
      if(isset($cfg['allow_search']))
      {
        $search_fields[] = $v['id']; 
      }
    } 
    
    return $search_fields; 
  }
  
  public static function get_filters_choices($entity_id)
  {
    $choices = array();
    $choices[''] = '';
    $fields_query = db_query("select f.* from app_fields f where f.type in (" . fields_types::get_types_for_filters_list(). ") and f.entities_id='" . db_input($entity_id) . "' order by f.listing_sort_order, f.name");
    while($v = db_fetch_array($fields_query))
    {
      $choices[$v['id']] = fields_types::get_option($v['type'],'name',$v['name']); 
    } 
    
    return $choices;
  }
        
}