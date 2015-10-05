<?php

class fieldtype_user_accessgroups
{
  public $options;
  
  function __construct()
  {
    $this->options = array('name' => TEXT_FIELDTYPE_USER_ACCESSGROUP_TITLE);
  }
  
  function render($field,$obj,$params = array())
  {      
    if(($default_group_id = access_groups::get_default_group_id())>0 and strlen($obj['field_' . $field['id']])==0)
    {
      $value = $default_group_id;
    }
    else
    {
      $value = $obj['field_' . $field['id']];
    }
    
    return select_tag('fields[' . $field['id'] . ']',access_groups::get_choices(),$value,array('class'=>'form-control input-medium'));
  }
  
  function process($options)
  {
    return $options['value'];
  }
  
  function output($options)
  {
    return access_groups::get_name_by_id($options['value']);
  }
}