<?php

class fieldtype_input_url
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_INPUT_URL_TITLE);
  }
    
  function render($field,$obj,$params = array())
  {
    return input_tag('fields[' . $field['id'] . ']',$obj['field_' . $field['id']],array('class'=>'form-control input-large fieldtype_input_url' . ($field['is_required']==1 ? ' required':'')));
  }
  
  function process($options)
  {
    return $options['value'];
  }
  
  function output($options)
  {        
    if(strlen($options['value'])>0)
    {
      if(isset($options['is_export']))
      {
        return  (!stristr($options['value'],'://') ? 'http://' . $options['value'] : $options['value']);
      }
      else
      {
        return '<a href="' . (!stristr($options['value'],'://') ? 'http://' . $options['value'] : $options['value']) . '" target="blank">' . TEXT_VIEW. '</a>';
      }
    }
    else
    {
      return '';
    }
  }
}