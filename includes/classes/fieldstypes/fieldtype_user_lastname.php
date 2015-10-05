<?php

class fieldtype_user_lastname
{
  public $options;
  
  function __construct()
  {
    $this->options = array('name' => TEXT_FIELDTYPE_USER_LASTNAME_TITLE);
  }
  
  function render($field,$obj,$params = array())
  {
    return input_tag('fields[' . $field['id'] . ']',$obj['field_' . $field['id']],array('class'=>'form-control input-medium required'));
  }
  
  function process($options)
  {
    return $options['value'];
  }
  
  function output($options)
  {
    return $options['value'];
  }
}