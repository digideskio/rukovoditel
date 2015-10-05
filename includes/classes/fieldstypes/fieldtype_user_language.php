<?php

class fieldtype_user_language
{
  public $options;
  
  function __construct()
  {
    $this->options = array('name' => TEXT_FIELDTYPE_USER_LANGUAGE_TITLE);
  }
  
  function render($field,$obj,$params = array())
  {
    return select_tag('fields[' . $field['id'] . ']',app_get_languages_choices(),$obj['field_' . $field['id']],array('class'=>'form-control input-medium required'));
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