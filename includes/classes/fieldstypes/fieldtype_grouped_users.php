<?php

class fieldtype_grouped_users
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_GROUPEDUSERS_TITLE,'has_choices'=>true);
  }
    
  function render($field,$obj,$params = array())
  {                
    $attributes = array('class'=>'form-control input-medium field_' . $field['id'] . ($field['is_required']==1 ? ' required':''));
                        
    $choices = fields_choices::get_choices($field['id'],($field['is_required']==1 ? false:true));
    
    $value = $obj['field_' . $field['id']];
    $value = ($value>0 ? substr($value,0,strpos($value,'|')) : fields_choices::get_default_id($field['id'])); 
    
    return select_tag('fields[' . $field['id'] . ']',$choices,$value,$attributes);
  }
  
  function process($options)
  {
    global $app_send_to;
    
    $choice_id = $options['value'];
    
    $value = '';
    $choice_query = db_query("select * from app_fields_choices where id='" . db_input($choice_id) . "'");
    if($choice = db_fetch_array($choice_query))
    {
      $value = $choice_id  . '|'. $choice['users'];
      
      foreach(explode(',',$choice['users']) as $id)
      {
        $app_send_to[] = $id;
      }
    }
    
    return $value;
  }
  
  function output($options)
  {
    $value = substr($options['value'],0,strpos($options['value'],'|'));
    return fields_choices::render_value($value,$options['choices_cache']);
  } 
  
  function reports_query($options)
  {
    $filters = $options['filters'];
    $sql_query = $options['sql_query'];
  
    $sql_query[] = "SUBSTRING(field_" . $filters['fields_id'] . ",1,locate('|',field_" . $filters['fields_id'] . ")-1)" .  ($filters['filters_condition']=='include' ? ' in ': ' not in ') .'(' . $filters['filters_values'] . ') ';
    
    return $sql_query;
  } 
}