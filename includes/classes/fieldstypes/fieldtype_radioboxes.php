<?php

class fieldtype_radioboxes
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_RADIOBOXES_TITLE,'has_choices'=>true);
  }
  
  function get_configuration()
  {
    $cfg = array();
            
    return $cfg;
  }  
  
  function render($field,$obj,$params = array())
  {                
    $attributes = array('class'=>'field_' . $field['id'] . ($field['is_required']==1 ? ' required':''));
                        
    $choices = fields_choices::get_choices($field['id'],false);
    
    $value = ($obj['field_' . $field['id']]>0 ? $obj['field_' . $field['id']] : fields_choices::get_default_id($field['id'])); 
    
    return '<div class="' . ($field['is_required']==1 ? ' required':'') . '">' . select_radioboxes_tag('fields[' . $field['id'] . ']',$choices,$value,$attributes) . '</div>';
  }
  
  function process($options)
  {            
    return $options['value'];
  }
  
  function output($options)
  {
    return fields_choices::render_value($options['value'],$options['choices_cache']);
  }
  
  function reports_query($options)
  {
    $filters = $options['filters'];
    $sql_query = $options['sql_query'];
  
    $sql_query[] = 'field_' . $filters['fields_id'] .  ($filters['filters_condition']=='include' ? ' in ': ' not in ') .'(' . $filters['filters_values'] . ') ';
    
    return $sql_query;
  }  
}