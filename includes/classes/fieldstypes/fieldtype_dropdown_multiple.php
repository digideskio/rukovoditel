<?php

class fieldtype_dropdown_multiple
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_DROPDOWN_MULTIPLE_TITLE,'has_choices'=>true);
  }
  
  function get_configuration()
  {
    $cfg = array();

    $cfg[] = array('title'=>TEXT_WIDHT, 
                   'name'=>'width',
                   'type'=>'dropdown',
                   'choices'=>array('input-small'=>TEXT_INPTUT_SMALL,'input-medium'=>TEXT_INPUT_MEDIUM,'input-large'=>TEXT_INPUT_LARGE,'input-xlarge'=>TEXT_INPUT_XLARGE),
                   'tooltip'=>TEXT_ENTER_WIDTH,
                   'params'=>array('class'=>'form-control input-medium'));                        
    
    return $cfg;
  }  
  
  function render($field,$obj,$params = array())
  {
    $cfg = fields_types::parse_configuration($field['configuration']);
            
    $attributes = array('class'=>'form-control ' . $cfg['width'] . ' chosen-select field_' . $field['id'] . ($field['is_required']==1 ? ' required':''),
                        'multiple'=>'multiple',
                        'data-placeholder'=>TEXT_SELECT_SOME_VALUES);
                        
    $choices = fields_choices::get_choices($field['id'],($field['is_required']==1 ? false:true));
    
    $value = ($obj['field_' . $field['id']]>0 ? $obj['field_' . $field['id']] : ($params['form']=='comment' ? '':fields_choices::get_default_id($field['id']))); 
    
    return select_tag('fields[' . $field['id'] . '][]',$choices,explode(',',$value),$attributes);
  }
  
  function process($options)
  {
    return implode(',',$options['value']);
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