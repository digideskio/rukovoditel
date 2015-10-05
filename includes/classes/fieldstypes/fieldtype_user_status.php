<?php

class fieldtype_user_status
{
  public $options;
  
  function __construct()
  {
    $this->options = array('name' => TEXT_FIELDTYPE_USER_STATUS_TITLE);
  }
  
  function render($field,$obj,$params = array())
  {
    $value = $obj['field_' . $field['id']];
    if(strlen($value)==0) $value = 1;
    
    return select_tag('fields[' . $field['id'] . ']',array('1'=>TEXT_ACTIVE,'0'=>TEXT_INACTIVE),$value,array('class'=>'form-control input-small'));
  }
  
  function process($options)
  {
    return $options['value'];
  }
  
  function output($options)
  {
    return ($options['value']==1 ? TEXT_ACTIVE : TEXT_INACTIVE);
  }
}