<?php

class fieldtype_checkboxes
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_CHECKBOXES_TITLE,'has_choices'=>true);
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
    
    return '<div class="' . ($field['is_required']==1 ? ' required':'') . '">' . select_checkboxes_tag('fields[' . $field['id'] . ']',$choices,$value,$attributes) . '</div>';
  }
  
  function process($options)
  {            
    return (is_array($options['value']) ? implode(',',$options['value']) : $options['value']);
  }
  
  function output($options)
  {
    if(isset($options['is_export']))
    {
      return fields_choices::render_value($options['value'],$options['choices_cache'],true) ;
    }
    else
    {
      return fields_choices::render_value($options['value'],$options['choices_cache']);
    }
  }  
  
  function reports_query($options)
  {
    $filters = $options['filters'];
    $sql_query = $options['sql_query'];
  
    $sql = array();
    
    foreach(explode(',',$filters['filters_values']) as $v)
    {
      $sql[] = ($filters['filters_condition']=='include' ? '': '!') . "find_in_set(" . $v . ",field_" . $filters['fields_id'] . ")";
    }
    
    $sql_query[] = '(' . implode(' or ',$sql) . ')';
              
    return $sql_query;
  }
}